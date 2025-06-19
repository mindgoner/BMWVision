from flask import Flask, render_template, Response
import cv2
import numpy as np

app = Flask(__name__)
cap = cv2.VideoCapture(1)  # Można zamienić na ścieżkę RTSP lub plik

def detect_lane(frame):
    height, width = frame.shape[:2]

    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    blur = cv2.GaussianBlur(gray, (5, 5), 0)
    edges = cv2.Canny(blur, 50, 150)

    # TRÓJKĄTNA MASKA: lewy dół, prawy dół, środek górny (25% wysokości)
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
                continue  # Pionowa linia – pomijamy (nie ma nachylenia)

            slope = (y2 - y1) / (x2 - x1)
            if abs(slope) < 0.5:
                continue  # Zbyt pozioma – pomijamy

            if slope < 0:
                left_lines.append((x1, y1, x2, y2))
            else:
                right_lines.append((x1, y1, x2, y2))

    # Uśrednienie lewych/prawych linii
    def average_line(lines):
        if len(lines) == 0:
            return None
        x_coords = []
        y_coords = []
        for x1, y1, x2, y2 in lines:
            x_coords += [x1, x2]
            y_coords += [y1, y2]
        poly = np.polyfit(y_coords, x_coords, 1)  # x = m*y + b
        y1 = height
        y2 = int(height * 0.25)
        x1 = int(poly[0] * y1 + poly[1])
        x2 = int(poly[0] * y2 + poly[1])
        return (x1, y1, x2, y2)

    line_image = np.zeros_like(frame)

    left_avg = average_line(left_lines)
    right_avg = average_line(right_lines)

    if left_avg is not None:
        cv2.line(line_image, (left_avg[0], left_avg[1]), (left_avg[2], left_avg[3]), (255, 0, 0), 5)
    if right_avg is not None:
        cv2.line(line_image, (right_avg[0], right_avg[1]), (right_avg[2], right_avg[3]), (0, 255, 0), 5)

    result = cv2.addWeighted(frame, 0.8, line_image, 1, 1)
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
