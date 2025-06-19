from flask import Flask, render_template, Response
import cv2
import numpy as np
from collections import deque

app = Flask(__name__)
cap = cv2.VideoCapture(1)  # Zmień na 1 jeśli Twoja kamera to drugi port

# Bufory do stabilizacji linii (średnia z 10 ostatnich)
left_history = deque(maxlen=10)
right_history = deque(maxlen=10)
def detect_lane(frame):
    global left_history, right_history
    height, width = frame.shape[:2]

    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    blur = cv2.GaussianBlur(gray, (5, 5), 0)
    edges = cv2.Canny(blur, 50, 150)

    # Trójkątna maska
    triangle = np.array([[
        (0, height),
        (width, height),
        (int(width / 2), int(height * 0.25))
    ]], np.int32)

    mask = np.zeros_like(edges)
    cv2.fillPoly(mask, triangle, 255)
    masked_edges = cv2.bitwise_and(edges, mask)

    # Hough Transform
    lines = cv2.HoughLinesP(masked_edges, 1, np.pi / 180, threshold=50, minLineLength=100, maxLineGap=50)

    left_lines = []
    right_lines = []

    if lines is not None:
        for line in lines:
            x1, y1, x2, y2 = line[0]

            if x2 == x1:
                continue  # pionowa linia – pomijamy

            slope = (y2 - y1) / (x2 - x1)
            if abs(slope) < 0.5:
                continue  # za płaska – pomijamy

            if slope < 0:
                left_lines.append((x1, y1, x2, y2))
            else:
                right_lines.append((x1, y1, x2, y2))

    def average_line(lines, fallback_history):
        if len(lines) > 0:
            x_coords = []
            y_coords = []
            for x1, y1, x2, y2 in lines:
                x_coords += [x1, x2]
                y_coords += [y1, y2]
            poly = np.polyfit(y_coords, x_coords, 1)
            y1 = height
            y2 = int(height * 0.25)
            x1 = int(poly[0] * y1 + poly[1])
            x2 = int(poly[0] * y2 + poly[1])
            line = (x1, y1, x2, y2)
            fallback_history.append(line)
        elif len(fallback_history) > 0:
            avg = np.mean(fallback_history, axis=0).astype(int)
            line = tuple(avg)
        else:
            line = None
        return line

    left_avg = average_line(left_lines, left_history)
    right_avg = average_line(right_lines, right_history)

    result = frame.copy()

    if left_avg is not None and right_avg is not None:
        def get_line_eq(x1, y1, x2, y2):
            m = (x2 - x1) / (y2 - y1 + 30)  # skrócenie linii
            b = x1 - m * y1
            return m, b

        try:
            m_left, b_left = get_line_eq(*left_avg)
            m_right, b_right = get_line_eq(*right_avg)

            if m_left != m_right:
                y_cross = int((b_right - b_left) / (m_left - m_right))
                x_cross = int(m_left * y_cross + b_left)

                pezerox = int(width / 2) - 30

                # START i KONIEC
                P0 = (pezerox, height)  # punkt startowy (dolna krawędź)
                P2 = (x_cross, y_cross)             # punkt końcowy (przecięcie)

                # Punkt kontrolny – zakrzywienie (tu w prawo, zmień znak jeśli chcesz w lewo)
                bend_offset = pezerox - x_cross
                P1 = (
                    int((P0[0] + P2[0]) / 2 + bend_offset),
                    int((P0[1] + P2[1]) / 2 - 30)
                )

                # Punktowana krzywa Béziera
                num_points = 20
                for i in range(num_points + 1):
                    t = i / num_points
                    x = int((1 - t)**2 * P0[0] + 2 * (1 - t) * t * P1[0] + t**2 * P2[0])
                    y = int((1 - t)**2 * P0[1] + 2 * (1 - t) * t * P1[1] + t**2 * P2[1])
                    cv2.circle(result, (x, y), 2, (255, 255, 255), -1)

                # Trójkąt w punkcie przecięcia
                triangle_size = 3
                pts = np.array([[
                    (x_cross, y_cross - triangle_size),
                    (x_cross - triangle_size, y_cross + triangle_size),
                    (x_cross + triangle_size, y_cross + triangle_size)
                ]], np.int32)
                cv2.fillPoly(result, pts, (255, 255, 255))
        except ZeroDivisionError:
            pass

    return result


def generate_frames():
    while True:
        success, frame = cap.read()
        if not success:
            break
        lane_frame = detect_lane(frame)
        _, buffer = cv2.imencode('.jpg', lane_frame)
        frame_bytes = buffer.tobytes()
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/video')
def video():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
