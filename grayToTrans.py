import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt
import os
import colorsys

location = input("enter relative path (with extension):")
parts = location.split(".")
name = parts[0]
ext = parts[1]

im = cv.imread(name + "." + ext, cv.IMREAD_UNCHANGED)
if (im.shape[2] == 4 and len(parts) == 2):
	im[:,:,0:3][im[:,:,3] > 0] = 0;
	cv.imwrite(name + "_trans." + ext, im)
else:
	# min_c = np.min(im, axis=2)
	# im_rgba = cv.cvtColor(im, cv.COLOR_RGB2RGBA)
	# im_rgba[:,:,3] = 255
	# im_rgba[min_c > 235] = 0

	retr, im_bin = cv.threshold(im[:,:,3], 5, 255, cv.THRESH_BINARY)
	cv.destroyAllWindows()
	im2, contours, hierarchy = cv.findContours(im_bin,cv.RETR_EXTERNAL,cv.CHAIN_APPROX_SIMPLE)
	im_bin = np.zeros_like(im)
	cv.drawContours(im_bin, contours, -1, (255,255,255), 1)

	im_rgba = im

	# test_ts = np.arange(1,256)

	# for t in test_ts:
	# 	test_rbg = (im + t - 255) / test_t;

	# 	cv.cvtColor(test_rbg, cv.COLOR_BGR2HSV)

	for i, row in enumerate(im_rgba):
		print(i)
		for j, column in enumerate(row):
			if im_bin[i,j,0] == 0 or im_rgba[i,j,3] == 0:
				continue

			t = -1
			last_h = -1

			for test_t in range(1, 256):
				r = im_rgba[i,j,0]
				g = im_rgba[i,j,1]
				b = im_rgba[i,j,2]

				A = (r + test_t - 255) / test_t;
				B = (g + test_t - 255) / test_t;
				C = (b + test_t - 255) / test_t;

				if (A < 0 or A > 255 or B < 0 or B > 255 or C < 0 or C > 255):
					continue

				h = colorsys.rgb_to_hsv(A,B,C)[1]
				if (abs(h - last_h) < 0.01):
					t = test_t
					break
				else:
					last_h = h

			if t == -1:
				t = 255

			im_rgba[i,j,0:3] = 0
			im_rgba[i,j,3] = t

	im[:,:,0:3][im[:,:,3] > 0] = 0;
	cv.imwrite(name + "_trans." + ext, im_rgba)


 #0 to 1 input and output