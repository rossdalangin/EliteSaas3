from playwright.sync_api import sync_playwright
import os

def run_visual_check():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={'width': 1280, 'height': 800})
        page = context.new_page()

        # Assuming we can test with the local HTML if it reflects structure
        # Since I cannot run a full WP server, I'll update the test HTML if needed
        # But wait, there is homepage_test.html, let's see if we should update it

        page.goto(f"file://{os.getcwd()}/verification/homepage_test.html")
        page.wait_for_timeout(1000)

        # Header area
        page.screenshot(path="verification/screenshots/header_update.png", clip={'x': 0, 'y': 0, 'width': 1280, 'height': 150})

        # Pricing area - need to scroll or find it
        pricing_section = page.query_selector(".pricing-section")
        if pricing_section:
            pricing_section.scroll_into_view_if_needed()
            page.wait_for_timeout(500)
            page.screenshot(path="verification/screenshots/pricing_update.png")
        else:
            # Fallback if class is different in the test html
            page.screenshot(path="verification/screenshots/full_page_update.png", full_page=True)

        context.close()
        browser.close()

if __name__ == "__main__":
    if not os.path.exists("verification/screenshots"):
        os.makedirs("verification/screenshots")
    run_visual_check()
