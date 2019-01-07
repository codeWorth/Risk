import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt
import os

folder = "Risk_Game_Masks_2"
img_names = os.listdir(folder)[1:]
print(img_names)
print("Images:", img_names)

for img_name in img_names:
	print("Cropping", img_name)
	im = cv.cvtColor(cv.imread(folder + "/" + img_name), cv.COLOR_RGB2RGBA)
	(im[:,:,3])[im[:,:,0] == 0] = 0;
	x,y,w,h = cv.boundingRect(cv.findNonZero(im[:,:,0]))
	cv.imwrite(folder + "/" + str(x) + "x" + str(y) + "-" + str(w) + "x" + str(h) + "_trans_" + img_name, im[y:y+h, x:x+w])
