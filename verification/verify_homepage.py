from playwright.sync_api import sync_playwright
import os

def run_verification():
    with sync_playwright() as p:
        # Desktop centering check
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={'width': 1280, 'height': 800})
        page = context.new_page()

        # Use absolute path correctly for current environment
        page.goto(f"file://{os.getcwd()}/verification/homepage_test.html")
        page.wait_for_timeout(1000)

        # Take screenshot of centered desktop view
        page.screenshot(path="verification/screenshots/desktop_centering.png")

        context.close()

        # Mobile menu check
        mobile_context = browser.new_context(
            viewport={'width': 375, 'height': 667},
            is_mobile=True,
            record_video_dir="verification/videos"
        )
        mobile_page = mobile_context.new_page()

        mobile_page.goto(f"file://{os.getcwd()}/verification/homepage_test.html")
        mobile_page.wait_for_timeout(500)

        # Take initial mobile screenshot
        mobile_page.screenshot(path="verification/screenshots/mobile_initial.png")

        # Open menu
        mobile_page.click(".menu-toggle")
        mobile_page.wait_for_timeout(1000) # Wait for animation

        # Take screenshot of open mobile menu
        mobile_page.screenshot(path="verification/screenshots/mobile_menu_open.png")

        mobile_context.close()
        browser.close()

if __name__ == "__main__":
    run_verification()
