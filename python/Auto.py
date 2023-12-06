import requests
import time
import re
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.chrome.service import Service
from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.support.ui import Select
import os
import csv
import time
from datetime import datetime, timedelta
import json
from urllib.parse import urlparse


# Base URL Info
WAIT_SEC = 20
HOME_URL = 'https://amazon.co.jp'
LOGIN_URL = 'https://www.amazon.co.jp/ap/signin?openid.pape.max_auth_age=0&openid.return_to=https%3A%2F%2Fwww.amazon.co.jp%2F%3Fref_%3Dnav_ya_signin&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.assoc_handle=jpflex&openid.mode=checkid_setup&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0'
ALL_ADDRESS_URL = 'https://www.amazon.co.jp/a/addresses'
CREATE_SHIPADDRESS_URL = 'https://www.amazon.co.jp/a/addresses/add?ref=ya_address_book_add_button'
ORDERLIST_URL = 'https://www.amazon.co.jp/gp/css/order-history?ref_=nav_AccountFlyout_orders'

# Base Output CSV Arg
Arg = ['注文日', '予定日', 'Yahoo注文番号', 'Asin', 'code', '受取人名', '〒', '住所', '電話番号', '商品名', '個数', '販売額', '仕入額', 'ポイント', '結果', 'Amazon注文番号', '注文者情報']



def make_autoOrder_folder():
    folder_name = 'Auto_Buy_Result'

    if not os.path.exists(folder_name):
        os.mkdir(folder_name)
        print(f'Folder "{folder_name}" created successfully')
    else:
        print(f'Folder "{folder_name}" already exists')


def make_autoOrderResult_csv(data):
    folder_name = 'Auto_Buy_Result'
    csv_file_name = "自動購入結果" + datetime.today().strftime('%Y%m%d%H%M%S') + ".csv"
    csv_file_path = os.path.join(folder_name, csv_file_name)

    with open(csv_file_path, mode='a', newline="", encoding="utf-8", errors="replace") as csv_file:
        result_csv = csv.writer(csv_file)
        result_csv.writerow(Arg)

    result_csv.writerow(data)

    csv_file.close()


def file_reader():
    with open('Auto_Order_Info.txt', 'r', encoding='utf-8') as file:
        contents = file.read()

    lines = contents.split('\n')
    variables = {}
    for line in lines:
        if '=' in line:
            key, value = line.split('=')
            variables[key] = value

    user_email          = variables.get('USER_EMAIL')
    user_password       = variables.get('USER_PASSWORD')
    app_key             = variables.get('APP_KEY')
    item_sender         = variables.get('ITEM_SENDER')
    account_type        = variables.get('USER_ACCOUNT_TYPE')
    card_number         = variables.get('CARD_NUMBER')
    gift_message        = variables.get('GIFT_MESSAGE')
    delivery_message    = variables.get('DELIVERY_MESSAGE')
    placement_select    = variables.get('PLACEMENT_SELECT')
    order_path          = variables.get('ORDER_CSV')
    output_path         = variables.get('OUTPUT_CSV')

    return variables
    return user_email, user_password, int(account_type), card_number, item_sender, gift_message, delivery_message, int(placement_select), order_path, output_path

def start_driver():
    USERNAME = 'Sn!per'
    # Selenium用のウェブドライバーを初期化し、さまざまなオプションで安定した最適なパフォーマンスを得る。
    # Selenium用のChromeドライバーオプションを設定。
    options = webdriver.ChromeOptions()
    options.add_argument('--disable-extensions')  # クリーンなブラウジングセッションのためにブラウザ拡張を無効にする。
    options.add_argument('--start-maximized')  # ブラウザを最大化したウィンドウで開始。参考: https://stackoverflow.com/a/26283818/1689770
    options.add_argument('--no-sandbox')  # 互換性向上のためにサンドボックスを無効にする。参考: https://stackoverflow.com/a/50725918/1689770
    options.add_argument('--disable-dev-shm-usage')  # より安定した動作のためにこのオプションを追加。参考: https://stackoverflow.com/a/50725918/1689770

    # 主処理
    try:
        driver_path = ChromeDriverManager().install()
        service = Service(executable_path=driver_path)
        driver = webdriver.Chrome(service=service, options=options)

    except ValueError:
        # 最新バージョンのChromeドライバーを取得してインストール。
        url = r'https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions-with-downloads.json'
        response = requests.get(url)
        data_dict = response.json()
        latest_version = data_dict["channels"]["Stable"]["version"]

        driver_path = ChromeDriverManager(version=latest_version).install()
        service = Service(executable_path=driver_path)
        driver = webdriver.Chrome(service=service, options=options)

    except PermissionError:  # 暫定処理 参考: https://note.com/yuu________/n/n14d97c155e5e
        try:
            driver = webdriver.Chrome(service=Service(f'C:\\Users\\{USERNAME}\\.wdm\\drivers\\chromedriver\\win64\\116.0.5845.97\\chromedriver.exe'), options=options)
        except:
            driver = webdriver.Chrome(service=Service(f'C:\\Users\\{USERNAME}\\.wdm\\drivers\\chromedriver\\win64\\116.0.5845.96\\chromedriver.exe'), options=options)

    # ブラウザウィンドウを最大化。
    driver.maximize_window()
    # ウェブドライバの待機時間を設定。
    wait = WebDriverWait(driver, WAIT_SEC)
    return driver

def amazon_login(driver, user_email, user_password):
    driver.get(LOGIN_URL)
    time.sleep(2)

    email_input = driver.find_element(By.XPATH, "//input[@id='ap_email' and @type='email']")
    email_input.clear()
    email_input.send_keys(user_email)
    submit_input = driver.find_element(By.XPATH, "//input[@type='submit' and @id='continue']")
    submit_input.click()
    time.sleep(1)

    password_input = driver.find_element(By.XPATH, "//input[@id='ap_password' and @type='password']")
    password_input.clear()
    password_input.send_keys(user_password)
    submit_input = driver.find_element(By.XPATH, "//input[@type='submit' and @id='signInSubmit']")
    submit_input.click()
    time.sleep(5)

    # time.sleep(20)


## Basic account
def create_shipAddress_basic(driver, ship_fullname, ship_phone_number, ship_area_code, ship_local_code, ship_street, ship_building):
    driver.get(CREATE_SHIPADDRESS_URL)
    time.sleep(2)

    ship_name_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressFullName' and @type='text']")
    ship_name_input.clear()
    ship_name_input.send_keys(ship_fullname)

    ship_phone_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPhoneNumber' and @type='text']")
    ship_phone_input.clear()
    ship_phone_input.send_keys(ship_phone_number)

    ship_areacode_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPostalCodeOne' and @type='text']")
    ship_areacode_input.clear()
    ship_areacode_input.send_keys(ship_area_code)

    ship_localcode_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPostalCodeTwo' and @type='text']")
    ship_localcode_input.clear()
    ship_localcode_input.send_keys(ship_local_code)

    ship_street_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressLine2' and @type='text']")
    ship_street_input.clear()
    ship_street_input.send_keys(ship_street)

    ship_building_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterBuildingOrCompanyName' and @type='text']")
    ship_building_input.clear()
    ship_building_input.send_keys(ship_building)

    address_main_check = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-use-as-my-default' and @type='checkbox']")
    address_main_check.click()
    
    address_add_submit = driver.find_element(By.XPATH, "//input[@class='a-button-input' and @type='submit' and @aria-labelledby='address-ui-widgets-form-submit-button-announce']")
    address_add_submit.click()
    time.sleep(2)

def item_detail_basic(driver, item_detail_url, quantity):
    driver.get(item_detail_url)
    time.sleep(2)
    
    if(int(quantity) > 1):
        quantity_select = driver.find_element(By.XPATH, "//select[@id='quantity']")
        quantity_select_object = Select(quantity_select)
        quantity_select_object.select_by_value(quantity)

    gift_wrap_check = driver.find_element(By.XPATH, "//input[@id='gift-wrap' and @type='checkbox']")
    gift_wrap_check.click()
    buy_now_button = driver.find_element(By.XPATH, "//input[@type='submit' and @id='buy-now-button']")
    buy_now_button.click()
    time.sleep(3)

def purchase_confirm_basic(driver, quantity, gift_message, gift_sender, delivery_access_message, placement_select):
    if(int(quantity) > 1):
        for number in range(1, int(quantity)):
            gift_check_box_id = 'toggle-gift-item-checkbox-' + str(number)
            gift_check_box = driver.find_element(By.XPATH, f"//input[@type='checkbox' and @id='{gift_check_box_id}']")
            gift_check_box.click()
            time.sleep(1)

    save_gift_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-label='ギフトの設定を保存する']")
    save_gift_submit.click()
    time.sleep(5)

    save_payment_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='orderSummaryPrimaryActionBtn-announce']")
    save_payment_submit.click()
    time.sleep(7)

    if(int(quantity) > 1):
        add_giftwrapping_modal = driver.find_element(By.XPATH, "//a[contains(text(), 'ギフトオプションを変更する') and contains(@class, 'a-size-mini') and contains(@class, 'a-spacing-top-base')]")
    else:
        add_giftwrapping_modal = driver.find_element(By.XPATH, "//a[contains(text(), 'ギフト包装を追加する') and contains(@class, 'a-size-mini') and contains(@class, 'a-spacing-top-base')]")
    add_giftwrapping_modal.click()
    time.sleep(3)

    gift_message_input = driver.find_element(By.XPATH, "//textarea[@id='message-area-0']")
    gift_message_input.clear()
    gift_message_input.send_keys(gift_message)
    gift_sender_input = driver.find_element(By.XPATH, "//input[@id='gift-message-sender-input-0' and @type='text']")
    gift_sender_input.clear()
    gift_sender_input.send_keys(gift_sender)
    gift_option_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @data-testid='GiftOptions_saveButton-0']")
    gift_option_submit.click()
    time.sleep(5)

    add_delivery_modal = driver.find_element(By.XPATH, "//span[@class='a-declarative']/a[contains(@class, 'a-link-normal') and contains(text(), '配送指示(置き配含む)')]")
    add_delivery_modal.click()
    time.sleep(5)
    expend_span = driver.find_elements(By.XPATH, "//span[@class='a-expander-prompt' and text()='ほかのオプションを表示']/../../a")
    expend_span[1].click()

    placement_check = driver.find_elements(By.XPATH, "//input[@name='preferredDeliveryLocationAPARTMENT' and @type='radio']")
    placement_check[int(placement_select) - 1].click()
    time.sleep(2)
    
    sat_delivery_button = driver.find_elements(By.XPATH, "//span[contains(@class, 'ma-SATs-open') and contains(@class, 'weekend-delivery-preference-button')]/span[@class='a-button-inner']/button[@value='配送可' and text()='配送可']")
    sat_delivery_button[1].click()
    sun_delivery_button = driver.find_elements(By.XPATH, "//span[contains(@class, 'ma-SUNs-open') and contains(@class, 'weekend-delivery-preference-button')]/span[@class='a-button-inner']/button[@value='配送可' and text()='配送可']")
    sun_delivery_button[1].click()
    time.sleep(2)

    add_more_instruction = driver.find_elements(By.XPATH, "//a[contains(@class, 'ma-property-type-add-more-link') and text()='さらに配送指示を追加する']")
    add_more_instruction[1].click()
    add_more_instruction_span = driver.find_elements(By.XPATH, "//span[text()='お届け先／置き配場所の目印、アクセス方法']")
    add_more_instruction_span[1].click()
    delivery_access_message_input = driver.find_elements(By.XPATH, "//textarea[@name='freeTextInstruction']")
    delivery_access_message_input[1].clear()
    delivery_access_message_input[1].send_keys(delivery_access_message)
    time.sleep(2)

    save_delivery_option_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='cdp-save-button-announce']")
    save_delivery_option_submit.click()
    time.sleep(2)

    order_confirm_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='bottomSubmitOrderButtonId-announce']")
    order_confirm_submit.click()
    time.sleep(3)

## Business account
def eligible_invoice_check(driver, item_detail_url):
    driver.get(item_detail_url)
    time.sleep(2)
    
    try:
        eligible_invoice_element = driver.find_element(By.XPATH, "//a[contains(text(), '適格請求書の発行対象')]")
        eligible_invoice = 1

    except:
        eligible_invoice = 2

    return eligible_invoice

def create_shipAddress_business(driver, ship_fullname, ship_phone_number, ship_area_code, ship_local_code, ship_street_building):
    driver.get(CREATE_SHIPADDRESS_URL)
    time.sleep(2)

    ship_name_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressFullName' and @type='text']")
    ship_name_input.clear()
    ship_name_input.send_keys(ship_fullname)

    ship_areacode_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPostalCodeOne' and @type='text']")
    ship_areacode_input.clear()
    ship_areacode_input.send_keys(ship_area_code)

    ship_localcode_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPostalCodeTwo' and @type='text']")
    ship_localcode_input.clear()
    ship_localcode_input.send_keys(ship_local_code)
    time.sleep(2)

    ship_address2_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressLine2' and @type='text']")
    ship_address2_input.clear()
    ship_address2_input.send_keys(ship_street_building)

    ship_phone_input = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-enterAddressPhoneNumber' and @type='text']")
    ship_phone_input.clear()
    ship_phone_input.send_keys(ship_phone_number)

    address_main_check = driver.find_element(By.XPATH, "//input[@id='address-ui-widgets-use-as-my-default' and @type='checkbox']")
    address_main_check.click()
    
    address_add_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='address-ui-widgets-form-submit-button-announce']")
    address_add_submit.click()
    time.sleep(2)

def item_detail_business(driver, item_detail_url, quantity):
    driver.get(item_detail_url)
    time.sleep(3)

    if(int(quantity) > 1):
        quantity_select = driver.find_element(By.XPATH, "//input[@class='quantity-text-input-with-label' and @type='text']")
        quantity_select.click()
        quantity_select.clear()
        quantity_select.send_keys(quantity)



    gift_wrap_check = driver.find_element(By.XPATH, "//input[@id='gift-wrap' and @type='checkbox']")
    gift_wrap_check.click()
    add_cart_button = driver.find_element(By.XPATH, "//input[@type='submit' and @id='add-to-cart-button']")
    add_cart_button.click()
    time.sleep(3)
    retail_checkout_button = driver.find_element(By.XPATH, "//input[@type='submit' and @name='proceedToRetailCheckout']")
    retail_checkout_button.click()
    time.sleep(5)

def purchase_confirm_business(driver, card_number, quantity, gift_message, gift_sender, delivery_access_message, placement_select):
    auto_order_num_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='a-autoid-4-announce']")
    auto_order_num_submit.click()
    time.sleep(3)
    
    address_confirm_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='shipToThisAddressButton-announce']")
    address_confirm_submit.click()
    time.sleep(5)

    if(int(quantity) > 1):
        for number in range(1, int(quantity)):
            gift_check_box_id = 'toggle-gift-item-checkbox-' + str(number)
            gift_check_box = driver.find_element(By.XPATH, f"//input[@type='checkbox' and @id='{gift_check_box_id}']")
            gift_check_box.click()
            time.sleep(1)

    save_gift_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-label='ギフトの設定を保存する']")
    save_gift_submit.click()
    time.sleep(5)

    save_payment_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='orderSummaryPrimaryActionBtn-announce']")
    save_payment_submit.click()
    time.sleep(7)

    try:
        confirm_paymentCard_input = driver.find_element(By.XPATH, "//div[contains(@class, 'apx-add-credit-card-number')]/input[@type='text' and contains(@class, 'a-input-text')]")
        confirm_paymentCard_input.clear()
        confirm_paymentCard_input.send_keys(card_number)

        paymentCard_confirm_submit = driver.find_element(By.XPATH, "//button[@aria-label='お客様のカードを照合します']")
        paymentCard_confirm_submit.click()

        time.sleep(2)
        save_payment_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='orderSummaryPrimaryActionBtn-announce']")
        save_payment_submit.click()
        time.sleep(7)

    except:
        print('confirm payment card.')


    if(int(quantity) > 1):
        add_giftwrapping_modal = driver.find_element(By.XPATH, "//a[contains(text(), 'ギフトオプションを変更する') and contains(@class, 'a-size-mini') and contains(@class, 'a-spacing-top-base')]")
    else:
        add_giftwrapping_modal = driver.find_element(By.XPATH, "//a[contains(text(), 'ギフト包装を追加する') and contains(@class, 'a-size-mini') and contains(@class, 'a-spacing-top-base')]")
    add_giftwrapping_modal.click()
    time.sleep(3)

    gift_message_input = driver.find_element(By.XPATH, "//textarea[@id='message-area-0']")
    gift_message_input.clear()
    gift_message_input.send_keys(gift_message)
    gift_sender_input = driver.find_element(By.XPATH, "//input[@id='gift-message-sender-input-0' and @type='text']")
    gift_sender_input.clear()
    gift_sender_input.send_keys(gift_sender)
    gift_option_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @data-testid='GiftOptions_saveButton-0']")
    gift_option_submit.click()
    time.sleep(5)

    add_delivery_modal = driver.find_element(By.XPATH, "//span[@class='a-declarative']/a[contains(@class, 'a-link-normal') and contains(text(), '配送指示(置き配含む)')]")
    add_delivery_modal.click()
    time.sleep(5)

    expend_span1 = driver.find_element(By.XPATH, "//a[@id='deliveryTimesEditLink' and text()='編集']")
    expend_span1.click()
    time.sleep(2)
    sat_sun_check = driver.find_element(By.XPATH, "//label[contains(@for, '2ClosedCheckbox_')]")
    sat_sun_check.click()
    time.sleep(3)

    modal = driver.find_element(By.XPATH, "//div[@id='a-popover-content-2']")
    driver.execute_script("arguments[0].scrollTop += 350;", modal)
    time.sleep(3)

    expend_span2 = driver.find_element(By.XPATH, "//span[@id='deliveryInstructions_render_link']/a")
    expend_span2.click()
    time.sleep(3)
    placement_check = driver.find_elements(By.XPATH, "//input[@name='dropOffLocationRadioButton' and @type='radio']")
    placement_check[int(placement_select) - 1].click()
    time.sleep(2)
    delivery_access_message_input = driver.find_element(By.XPATH, "//textarea[@id='additionalInfo']")
    delivery_access_message_input.clear()
    delivery_access_message_input.send_keys(delivery_access_message)
    time.sleep(1)

    save_delivery_option_submit = driver.find_element(By.XPATH, "//input[@type='submit' and contains(@aria-labelledby, 'adpSubmitButton_')]")
    save_delivery_option_submit.click()
    time.sleep(3)

    order_confirm_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='bottomSubmitOrderButtonId-announce']")
    order_confirm_submit.click()
    time.sleep(3)


def order_completeConfirm(driver):
    driver.get(ORDERLIST_URL)
    time.sleep(5)

    driver.get(ALL_ADDRESS_URL)
    time.sleep(2)

    order_confirm_a = driver.find_element(By.XPATH, "//a[@id='ya-myab-address-delete-btn-0']")
    order_confirm_a.click()
    time.sleep(1)
    delete_address_submit = driver.find_element(By.XPATH, "//input[@type='submit' and @aria-labelledby='deleteAddressModal-0-submit-btn-announce']")
    delete_address_submit.click()
    time.sleep(2)

    driver.get(HOME_URL)
    time.sleep(5)






def auto_purchase(item, user_info, driver):
    # Base Info
    base_info = item.split(',')

    url = "https://xs767540.xsrv.jp/api/v1/get_shop"
    payload = 'key=' + user_info['APP_KEY'] + '&item_code=' + base_info[3].split('L1=')[1]
    headers = { 'Content-Type': 'application/x-www-form-urlencoded' }

    response = requests.request("POST", url, headers=headers, data=payload)
    decoded_data = json.loads(response.text)
    print(decoded_data)
    return
    item_detail_url = decoded_data['shopURL']
    # item_detail_url = 'https://www.amazon.co.jp/dp/B0773H76WK'
    print(item_detail_url)

    parsed_url = urlparse(item_detail_url)
    path_components = parsed_url.path.split('/')
    product_id = path_components[-1]
    if '?' in product_id:
        product_id = product_id.split('?')[0]
    else:
        product_id = 'Null'
    
    # Ship Info
    ship_fullname = base_info[6]
    ship_phone_number = base_info[16]
    ship_postalcode = base_info[11]
    ship_prefecture = base_info[12]
    ship_city = base_info[13]
    ship_address1 = base_info[14]
    ship_address2 = base_info[15]
    quantity = base_info[5].split('L1=')[1]

    index = re.search(r"\d", ship_address1).start()
    characters = ship_address1[:index]
    numbers = ship_address1[index:]

    ship_area_code = ship_postalcode[:3]
    ship_local_code = ship_postalcode[3:]
    ship_city_town = ship_city + characters
    ship_street = numbers
    ship_building = ship_address2
    ship_street_building = ship_street + ship_building
    gift_sender = user_info['ITEM_SENDER']
    delivery_access_message = ship_prefecture + ship_city + ship_address1 + ship_address2 + '\n' + user_info['DELIVERY_MESSAGE']
    

    if (user_info['USER_ACCOUNT_TYPE'] == '1'):
        print('Basic account')
        create_shipAddress_basic(driver, ship_fullname, ship_phone_number, ship_area_code, ship_local_code, ship_street, ship_building)
        item_detail_basic(driver, item_detail_url, quantity)
        purchase_confirm_basic(driver, quantity, user_info['GIFT_MESSAGE'], user_info['ITEM_SENDER'], delivery_access_message, user_info['PLACEMENT_SELECT'])

    elif (user_info['USER_ACCOUNT_TYPE'] == '2'):
        print('Business account')

        eligible_invoice = eligible_invoice_check(driver, item_detail_url)

        if (eligible_invoice == 1):
            create_shipAddress_business(driver, ship_fullname, ship_phone_number, ship_area_code, ship_local_code, ship_street_building)
            item_detail_business(driver, item_detail_url, quantity)
            purchase_confirm_business(driver, user_info['CARD_NUMBER'], quantity, user_info['GIFT_MESSAGE'], user_info['ITEM_SENDER'], delivery_access_message, user_info['PLACEMENT_SELECT'])

        else:
            print('No eligible invoice.')

    else:
        print('OK')

    order_completeConfirm(driver)


    order_time_str = base_info[0]
    order_time_obj = datetime.strptime(order_time_str, '%m/%d/%Y %I:%M:%S %p')
    order_time = order_time_obj.strftime('%m/%d/%Y')
    
    today = datetime.today().strftime('%Y-%m-%d')
    planned_date = (datetime.today() + timedelta(days=1)).strftime('%Y年%m月%d日')

    order_id = base_info[2]
    asin = product_id
    code = base_info[3].split('L1=')[1]
    recipient_name = base_info[6]
    zip_code = ship_postalcode
    address = ship_prefecture + ship_city + ship_address1 + ship_address2
    phone_number = ship_phone_number
    product_name = base_info[4].split('L1=')[1]
    quantity = quantity
    sell_price = base_info[7]
    buy_price = 0
    point = 0
    result = '注文済み'
    amazon_order_num = 0
    order_info = ''


    make_autoOrderResult_csv()



def main():
    user_info = file_reader()
    
    driver = start_driver()
    driver.maximize_window()
    
    amazon_login(driver, user_info['USER_EMAIL'], user_info['USER_PASSWORD'])

    try:
        with open(user_info['ORDER_CSV'], 'r', encoding='utf-8') as file:
            content = file.read()
            lines = content.split('\n')
            for idx, line in enumerate(lines):
                if (idx == 0 or idx == len(lines) - 1):
                    continue
                auto_purchase(line, user_info, driver)

    except FileNotFoundError:
        print("File not found.")


if __name__ == '__main__':
    main()
