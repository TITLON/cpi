from selenium import webdriver
from PIL import Image
import time

def capture_screenshot(url, output_file):
    # Set up the driver and open the URL
    options = webdriver.ChromeOptions()
    options.add_argument('--headless')  # Run Chrome in headless mode
    driver = webdriver.Chrome(options=options)
    driver.get(url)
    
    time.sleep(2)
    
    # Set window size to match content dimensions
    width = driver.execute_script("return document.body.scrollWidth")
    height = driver.execute_script("return document.body.scrollHeight")
    driver.set_window_size(width, height)

    # Capture the screenshot
    driver.save_screenshot(output_file)

    # Crop the image using Pillow (if needed)
    desired_width = 800
    desired_height = 600

    with Image.open(output_file) as img:
        img_cropped = img.crop((0, 0, desired_width, desired_height))
        img_cropped.save(output_file)

    # Close the driver
    driver.quit()


capture_screenshot("http://titlon.uit.no/cpi/", "kpi.png")
capture_screenshot("http://titlon.uit.no/cpi/kpi_jae.html", "kpi_jae.png")
capture_screenshot("http://titlon.uit.no/cpi/kpi_jae_instant.html", "kpi_jae_instant.png")