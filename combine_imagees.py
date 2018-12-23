import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt
import os

folder = "Risk_Game_Masks"
img_names = os.listdir(folder)[1:]
print("Images:", img_names)
imgs = []

im = cv.imread(folder + "/" + img_names[0])

for i in range(1,len(img_names)):
	print("Combining", img_names[i])
	im_add = cv.imread(folder + "/" + img_names[i])
	im[im_add == 255] = 255-i

cv.imwrite("total_imgs.png", im)