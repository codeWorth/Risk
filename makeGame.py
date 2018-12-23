import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt

size = 0.15

img = cv.imread("boardgame_colors.png")
imgHSV = cv.cvtColor(img, cv.COLOR_BGR2HSV)
minS = 150
minV = 170

ret, thresh1 = cv.threshold(imgHSV[:,:,1],minS,255,cv.THRESH_BINARY)
# cv.imshow("sat", cv.resize(thresh1, (0,0), fx=size, fy=size))
# cv.waitKey(0)

ret, thresh2 = cv.threshold(imgHSV[:,:,2],minV,255,cv.THRESH_BINARY)
# cv.imshow("val", cv.resize(thresh2, (0,0), fx=size, fy=size))
# cv.waitKey(0)

t1 = thresh1
t1[t1>0] = 1
t2 = thresh2
t2[t2>0] = 1

img2 = t1[...,None] * t2[...,None] * img
hsv2 = t1[...,None] * t2[...,None] * imgHSV

src = t1*t2*255

# cv.imshow("final", cv.resize(hsv2[:,:,0], (0,0), fx=size, fy=size))
# cv.waitKey(0)

cv.destroyAllWindows()

r=4

def click(event, x, y, flags, param):
	if event != cv.EVENT_LBUTTONDOWN:
		return

	for cnt in contours:
		if (cv.contourArea(cnt) > 1000):
			im = np.zeros_like(src)
			cv.drawContours(im, [cnt], 0, (255,0,0), -1)
			if (im[int(y/size),int(x/size)] == 255):
				cv.imshow("country", cv.resize(im, (0,0), fx=size, fy=size))
				name = input("What is this country's name: ")
				cv.imwrite(name+".png", im)
				print("Saved Image as " + name + ".png")
				return

for i in range(0,360,5):
	img3 = src.copy()
	img3[hsv2[:,:,0] < i-r] = 0
	img3[hsv2[:,:,0] > i+r] = 0

	cntImg, contours, h = cv.findContours(img3, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE)
	cv.drawContours(img3, contours, -1, (150,0,0), -1)

	cv.imshow(str(i), cv.resize(img3, (0,0), fx=size, fy=size))
	cv.setMouseCallback(str(i), click)
	if (cv.waitKey(0) == 27):
		break

	cv.destroyAllWindows()


cv.destroyAllWindows()
