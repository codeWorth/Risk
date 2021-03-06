import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt
import os

folder = "Risk_Game_Masks_2"
img_names = os.listdir(folder)#[1:]

for img_name in img_names:
	print("Processing", img_name)
	im = cv.imread(folder + "/" + img_name)[:,:,0].copy()
	im2 = np.zeros_like(im)
	cntImg, contours, h = cv.findContours(im, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE)
	cv.drawContours(im2, contours, -1, (255,0,0), 14)
	cv.imwrite(folder + "/outline_" + img_name, im2)

