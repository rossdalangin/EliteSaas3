from playwright.sync_api import sync_playwright
import os

def run_visual_check(page):
    # Path to the mock HTML file
    current_dir = os.getcwd()
    file_path = f"file://{current_dir}/verification/homepage_test.html"

    page.goto(file_path)
    page.wait_for_timeout(1000)

    # 1. Desktop View - Pricing Section
    page.set_viewport_size({"width": 1280, "height": 800})
    # Scroll to pricing
    page.evaluate("window.scrollTo(0, 200)")
    page.wait_for_timeout(500)
    page.screenshot(path="/home/jules/verification/screenshots/pricing_update.png")

    # 2. Desktop View - Header CTA
    page.evaluate("window.scrollTo(0, 0)")
    page.wait_for_timeout(500)
    page.screenshot(path="/home/jules/verification/screenshots/header_update.png")

    # 3. Mobile View - Menu
    page.set_viewport_size({"width": 375, "height": 667})
    page.wait_for_timeout(500)
    page.screenshot(path="/home/jules/verification/screenshots/mobile_initial.png")

    # Open Menu
    page.click(".menu-toggle")
    page.wait_for_timeout(500)
    page.screenshot(path="/home/jules/verification/screenshots/mobile_menu_open.png")

if __name__ == "__main__":
    os.makedirs("/home/jules/verification/screenshots", exist_ok=True)
    os.makedirs("/home/jules/verification/videos", exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            record_video_dir="/home/jules/verification/videos"
        )
        page = context.new_page()
        try:
            run_visual_check(page)
        finally:
            context.close()
            browser.close()
