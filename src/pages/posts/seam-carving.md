---
layout: "../../layouts/PostLayout.astro"
title: "image resizing images using dynamic programming"
description: "seam carving algorithm's explanation & implementation"
pubDate: "2025-10-24"
medium: "https://medium.com/@inth3l00p/image-resizing-images-using-dynamic-programming-693d81b88ed6"
source: "https://github.com/in-th3-l00p/seam-carving"
live: "https://seamcarving.tiscacatalin.com/"
---

You’ve likely come across a situation where your image looks great, but its dimensions aren’t the ones you need for a particular format or layout.

Scaling or cropping will destroy your picture, but this problem might have a fix. **Seam carving** is an algorithm for **content-aware image resizing**. Convince yourself what this does to your image from the following example:

![original image of a castle & a person](https://cdn-images-1.medium.com/max/2856/0*zHauTLwZkywhJdHl.jpg)*original image of a castle & a person*

![original image resized using scaling (left) & seam carving (right)](https://cdn-images-1.medium.com/max/3828/1*3RzxSuVleyemapamiYAI8g.png)*original image resized using scaling (left) & seam carving (right)*

## the theory beneath seam carving

A one-sentence description of it is: *to remove a line or a column, choose the **seam** (either a vertical or horizontal one) that is **the least noticeable**, remove it & repeat this process until the result has the desired dimensions.*

![a horizontal seam of the castle image from above](https://cdn-images-1.medium.com/max/2000/0*0s5H7S4h7Gp3ZyWp.png)*a horizontal seam of the castle image from above*

As seen in the provided example, a **seam** is a connected path of pixels, extending from top to bottom (vertically) or from left to right (horizontally). The first important concept of the algorithm is the method of calculating *the least noticeable seam*.

### mathematical core

The **energy map** measures the visual importance of each pixel. You can think of it as a function ***E*(*x*,*y*)** that, given the coordinates of a pixel, returns how significant that pixel is to the image’s structure.

It isn’t tied to a single formula; the most common option is the gradient magnitude, which measures how quickly pixel intensity changes. Other approaches include Laplacian filters, entropy texture measures, and deep learning models that highlight important objects.

![gradient magnitude energy map; I(x, y) — numeric pixel value of image](https://cdn-images-1.medium.com/max/2040/1*PoClt5nFJFHrwJEhaFLf3w@2x.png)*gradient magnitude energy map; I(x, y) — numeric pixel value of image*

The calculation of ***E*(*x*,*y*)** using gradient magnitude is done using a filter called the [**Sobel operator**](https://en.wikipedia.org/wiki/Sobel_operator). The *Sobel operator* uses two small *3×3* filters that slide over the image to estimate how the pixel values change horizontally and vertically.

![the Sobel operator applied to a picture](https://cdn-images-1.medium.com/max/2560/1*0UFv-RdNOR-fVAAetQApbQ.png)*the Sobel operator applied to a picture*

In this context, a kernel is a small matrix that slides over an image, and convolution is the process of doing element-wise multiplication between the kernel and the pixel values under it, then adding up the results.

These are the two kernels of the *Sobel operator*:

![](https://cdn-images-1.medium.com/max/2764/1*p-W370v0nrHBidK2ELniBQ@2x.png)

A notable observation is that the Sobel operator effectively detects the edges of an image. ***Gₓ*** is detecting the vertical edges, while ***Gᵧ*** highlights the horizontal ones. In fact, ***E*(*x*,*y*)** is a filter that measures the *edge strength* of each pixel.

### discovering the seams

Now that the required mathematics have been introduced, it’s time to find out how we can use the energy map to get the actual seam.

To achieve this, **dynamic programming** is used to build a table that tracks the cheapest possible seam, based on **the best choices from the row (or column) above it**.

![dynamic programming recurrence relation for a vertical seam](https://cdn-images-1.medium.com/max/4408/1*CudpumQ80-npv_6GRMdIWQ@2x.png)*dynamic programming recurrence relation for a vertical seam*

For horizontal seams, the idea is the same, just replace the pixels above with the three neighboring pixels to the left.

Getting the *least noticeable seam* now is just backtracking the table built. This process is easily explained through a few lines of code:

    x = np.argmin(M[-1]) // minimum element of the last row
    seam = [] // seam coordinate array
    
    // backtracking
    for y in reversed(range(height)):
        seam.append((y, x))
    
        // the next upper element can be either on NW, N, or NE
        left  = M[y-1, x-1] if x > 0 else float('inf')
        up    = M[y-1, x]
        right = M[y-1, x+1] if x < width-1 else float('inf')
        x += np.argmin([left, up, right]) - 1

Afterwards, just remove the coordinates of the seam array to get rid of a unit from the width/height of the image.

### the order of seam carving

Suppose that we want to seam carve an *n x m image* to *n’ x m’*, where *n > n’ *and* m > m’*, therefore we have to remove seams that are horizontal and vertical as well. A good question, definitely, is how we choose between removing a horizontal seam or a vertical one.

To find the answer, keep in mind that the goal of seam carving is to resize the image by removing the pixels with the* lowest energy*. The approach is to compute both candidate seams and remove the one with the lower total energy, ensuring minimal visual distortion.

## getting practical

Time to start writing a *Python* program that does exactly this. The first step is getting the images as a *2D matrix* that’ll let us do all these operations.

    import cv2
    image = cv2.imread("castle.jpg")

This is easily done using [**OpenCV**](https://pypi.org/project/opencv-python/). Next, it’s time to compute the **energy map** of it:

### the energy map

    import cv2
    import numpy as np
    
    def convolve_3x3(gray: np.ndarray, kernel: np.ndarray) -> np.ndarray:
      """
      performs a 3x3 convolution on a grayscale image
      - gray: 2D float32 array
      - kernel: 3x3 float32 array
      returns a 2D float32 array of the same size
      """
    
      H, W = gray.shape
      output = np.zeros((H, W), dtype=np.float32)
    
      # we ignore borders by replicating the edge pixel (same behavior as OpenCV BORDER_REPLICATE)
      for y in range(H):
        for x in range(W):
          # collect the 3x3 neighborhood (clamped to image boundaries)
          acc = 0.0
          for ky in range(-1, 2):
            for kx in range(-1, 2):
              ny = min(max(y + ky, 0), H - 1)  # clamp between 0 and H-1
              nx = min(max(x + kx, 0), W - 1)  # clamp between 0 and W-1
              acc += gray[ny, nx] * kernel[ky + 1, kx + 1]
    
          output[y, x] = acc
    
      return output
    
    def energy_map_sobel(img: np.ndarray) -> np.ndarray:
      """
      computes the Sobel-based energy map of an image
      returns a float32 2D matrix in [0,1]
      """
    
      # 1. convert to grayscale float32 in [0,1]
      gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY).astype(np.float32)
      gray /= 255.0
    
      # 2. defining the sobel kernels (3x3)
      Gx = np.array([[-1, 0, 1],
                     [-2, 0, 2],
                     [-1, 0, 1]], dtype=np.float32)
    
      Gy = np.array([[-1, -2, -1],
                     [ 0,  0,  0],
                     [ 1,  2,  1]], dtype=np.float32)
    
      # 3 convolution to get derivatives
      dx = convolve_3x3(gray, Gx)
      dy = convolve_3x3(gray, Gy)
    
      # ) L1 gradient magnitude: |dx| + |dy|
      E = np.abs(dx) + np.abs(dy)
    
      # 5 normalize to [0,1]
      E -= E.min()
      max_val = E.max()
      if max_val > 0:
        E /= max_val
    
      return E

The energy_map_sobel function returns the *energy map *as a 2D matrix, receiving the *image matrix* as a parameter.

First, it converts the received matrix from its RGB representation to grayscale; therefore, the gray is a *width* x *height* matrix of float values from 0 to 1.

Second, it applies the Sobel kernels using convolve_3x3, a function that performs convolution with 3×3 kernels. Convolution multiplies each kernel element by the corresponding neighboring pixel and sums the results, producing a new value that highlights local intensity changes.

After applying the kernels, we can add up the result of the vertical and the horizontal filter, normalize it again, and then return. The energy map is calculated.

### backtracking

Once the energy map is ready, we extract the seam by backtracking: we start from the lowest energy pixel in the bottom row and step upward each time to the neighboring pixel with the smallest value, tracing the least noticeable path through the image & pushing the coordinates in an array.

    def backtrack_vertical_seam(M: np.ndarray) -> np.ndarray:
      """
      recover minimal-energy vertical seam from DP table M
      returns seam_x of shape (H,), where seam_x[y] is the column index at row y
      """
      H, W = M.shape
      seam = np.empty(H, dtype=np.int32)
    
      # start from the minimum in the last row
      x = int(np.argmin(M[-1]))
      seam[H - 1] = x
    
      for y in range(H - 2, -1, -1):
        # pick among (x-1, x, x+1) in row y the one with the smallest value
        x_candidates = [x]
        if x > 0:
          x_candidates.append(x - 1)
        if x < W - 1:
          x_candidates.append(x + 1)
    
        # choose argmin among the candidates
        x = min(x_candidates, key=lambda xx: M[y, xx])
        seam[y] = x
    
      return seam

### putting everything together

We’re able to get the desired seam. In case you’re wondering what’s changed when computing the horizontal seam: the process is identical, but it runs from left to right instead of top to bottom, using the three neighboring pixels on the left rather than the ones above.

Considering the following function that removes the seam from the original image:

    def remove_vertical_seam(img: np.ndarray, seam_x: np.ndarray) -> np.ndarray:
      """
      remove one vertical seam from an image (H,W,3) or (H,W)
      returns a new array with width-1
      """
      H, W = img.shape[:2]
      if img.ndim == 2:
        out = np.empty((H, W - 1), dtype=img.dtype)
        for y in range(H):
          x = seam_x[y]
          out[y, :x] = img[y, :x]
          out[y, x:] = img[y, x + 1:]
        return out
      else:
        C = img.shape[2]
        out = np.empty((H, W - 1, C), dtype=img.dtype)
        for y in range(H):
          x = seam_x[y]
          out[y, :x, :] = img[y, :x, :]
          out[y, x:, :] = img[y, x + 1:, :]
        return out

The function that provides abstraction for the entire implementation, and simply gets the image to the desired dimensions, looks like this:

    def seam_carve_resize(img, target_w, target_h):
      """
      shrink image to (target_h, target_w) by always picking the cheaper seam:
      at each step, compute both candidate seams (vertical + horizontal) on the
      current energy map and remove the one with the lower total energy.
      """
      out = img.copy()
      h, w = out.shape[:2]
    
      if target_w > w or target_h > h:
        raise ValueError("this function only shrinks (targets must be <= current size)")
    
      while w > target_w or h > target_h:
        e = energy_map_sobel(out)
    
        # only one direction left to shrink
        if w > target_w and h == target_h:
          mv = dp_cumulative_energy_vertical(e)
          seam_x = backtrack_vertical_seam(mv)
          out = remove_vertical_seam(out, seam_x)
          w -= 1
          continue
    
        if h > target_h and w == target_w:
          mh = dp_cumulative_energy_horizontal(e)
          seam_y = backtrack_horizontal_seam(mh)
          out = remove_horizontal_seam(out, seam_y)
          h -= 1
          continue
    
        # both directions needed: compute both candidates and compare total energy on E
        mv = dp_cumulative_energy_vertical(e)
        seam_x = backtrack_vertical_seam(mv)
        cost_v = float(e[np.arange(h), seam_x].sum())
    
        mh = dp_cumulative_energy_horizontal(e)
        seam_y = backtrack_horizontal_seam(mh)
        cost_h = float(e[seam_y, np.arange(w)].sum())
    
        if cost_v <= cost_h and w > target_w:
          out = remove_vertical_seam(out, seam_x)
          w -= 1
        else:
          out = remove_horizontal_seam(out, seam_y)
          h -= 1
    
      return out

As described in the mathematical description of the algorithm, we have to choose at each removal whether we’re removing a horizontal or vertical seam. This is it, seam carving implementation in Python.

## conclusion & more about the algorithm

This was a focused walkthrough of seam carving’s core ideas. With these fundamentals: energy maps, dynamic programming, and seam removal, you now have the backbone of content-aware image resizing. From here, you can extend the method for speed, enlargement, or object removal.

algorithm’s whitepaper: [https://perso.crans.org/frenoy/matlab2012/seamcarving.pdf](https://perso.crans.org/frenoy/matlab2012/seamcarving.pdf)
