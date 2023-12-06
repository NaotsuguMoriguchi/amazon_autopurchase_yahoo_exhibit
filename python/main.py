import tkinter as tk
from tkinter import filedialog, messagebox
import codecs
from Auto import main


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

    return user_email, user_password, app_key, int(account_type), card_number, item_sender, gift_message, delivery_message, int(placement_select), order_path, output_path


def draw_window():
    user_email, user_password, app_key, account_type, card_number, item_sender, gift_message, delivery_message, placement_select, order_path, output_path = file_reader()
    
    window = tk.Tk()
    window.geometry('800x600')
    window.title('Amazon自動購入ツール_v_1.0')

    ## Setting, Message, Console  frame:
    frame_setting = tk.Frame(window, width=380, height=400, highlightbackground='lightgray', highlightthickness=1)
    frame_setting.pack()
    frame_setting.place(x=15, y=20)

    frame_message = tk.Frame(window, width=380, height=400, highlightbackground='lightgray', highlightthickness=1)
    frame_message.pack()
    frame_message.place(x=405, y=20)

    frame_console = tk.Frame(window, width=770, height=130,  highlightbackground='lightgray', highlightthickness=1)
    frame_console.pack()
    frame_console.place(x=15, y=450)



    ## User information

    # input csv (order csv)
    label_ordercsv = tk.Label(frame_setting, text='注文CSV：', width=20)
    label_ordercsv.pack()
    label_ordercsv.place(x=10, y=30)

    entry_ordercsv = tk.Entry(frame_setting, width=20)
    entry_ordercsv.pack()
    entry_ordercsv.place(x=120, y=30)

    def handle_ordercsv():
        global file_path
        file_path = filedialog.askopenfilename()
        entry_ordercsv.insert(0, file_path)
        # print("Selected File:", file_path)

    button_ordercsv = tk.Button(frame_setting, text='参照', width=7, command=handle_ordercsv)
    button_ordercsv.pack()
    button_ordercsv.place(x=250, y=27)

    # output csv path (result csv path)
    label_outputcsv = tk.Label(frame_setting, text='出力CSV：', width=20)
    label_outputcsv.pack()
    label_outputcsv.place(x=10, y=60)

    entry_outputcsv = tk.Entry(frame_setting, width=20)
    entry_outputcsv.pack()
    entry_outputcsv.place(x=120, y=60)

    def handle_outputcsv():
        global folder_path
        folder_path = filedialog.askdirectory()
        entry_outputcsv.insert(0, folder_path)
        # print("Selected Folder:", folder_path)

    button_outputcsv = tk.Button(frame_setting, text='参照', width=7, command=handle_outputcsv)
    button_outputcsv.pack()
    button_outputcsv.place(x=250, y=57)


    # login email
    label_email = tk.Label(frame_setting, text='メール：', width=20)
    label_email.pack()
    label_email.place(x=10, y=110)

    entry_email = tk.Entry(frame_setting, width=30)
    entry_email.pack()
    entry_email.place(x=120, y=110)
    entry_email.insert(0, user_email)

    # login password
    label_password = tk.Label(frame_setting, text='パスワード：', width=20)
    label_password.pack()
    label_password.place(x=10, y=140)

    entry_password = tk.Entry(frame_setting, width=30)
    entry_password.pack()
    entry_password.place(x=120, y=140)
    entry_password.insert(0, user_password)


    # account type
    options1 = ['玄関', '宅配ボックス', 'ガスメーターボックス', '自転車かご', '車庫', '建物内受付/管理人', '置き配を利用しない']
    options2 = ['建物内受付/管理人', '玄関', '在宅ボックス', 'ガスメーターボックス', '在庫', '自転車かご', '置き配を利用しない']

    def callback_option():
        if (checked_var.get() == 0):
            selected_option.set(options1[0])
            selected_box = tk.OptionMenu(frame_setting, selected_option, *options1)
        elif (checked_var.get() == 1):
            selected_option.set(options2[0])
            selected_box = tk.OptionMenu(frame_setting, selected_option, *options2)

        selected_box.pack()
        selected_box.place(x=120, y=260)
        selected_box.config(width=20)

    checked_var = tk.IntVar()
    if (account_type == 2):
        checked_var.set(1)

    chkbox_business = tk.Checkbutton(frame_setting, text='ビジネスアカウント', command=callback_option, variable=checked_var)
    chkbox_business.pack()
    chkbox_business.place(x=120, y=170)

    # account card number
    label_cardnumber = tk.Label(frame_setting, text='カード番号：', width=20)
    label_cardnumber.pack()
    label_cardnumber.place(x=10, y=200)

    entry_cardnumber = tk.Entry(frame_setting, width=30)
    entry_cardnumber.pack()
    entry_cardnumber.place(x=120, y=200)
    entry_cardnumber.insert(0, card_number)

    # item sender
    label_itemsender = tk.Label(frame_setting, text='差出人：', width=20)
    label_itemsender.pack()
    label_itemsender.place(x=10, y=230)

    entry_itemsender = tk.Entry(frame_setting, width=30)
    entry_itemsender.pack()
    entry_itemsender.place(x=120, y=230)
    entry_itemsender.insert(0, item_sender)

    # placement
    selected_option = tk.StringVar(frame_setting)
    if (account_type == 2):
        selected_option.set(options2[placement_select - 1])
        option = options2
    else :
        selected_option.set(options1[placement_select - 1])
        option = options1

    selected_box = tk.OptionMenu(frame_setting, selected_option, *option)
    selected_box.pack()
    selected_box.place(x=120, y=260)
    selected_box.config(width=20)


    # tool key
    label_appkey = tk.Label(frame_setting, text='アプリキー：', width=20)
    label_appkey.pack()
    label_appkey.place(x=10, y=320)

    global entry_appkey
    entry_appkey = tk.Entry(frame_setting, width=30)
    entry_appkey.pack()
    entry_appkey.place(x=120, y=320)
    entry_appkey.insert(0, app_key)
    

    # gift message
    label_giftmsg = tk.Label(frame_message, text='ギフトメッセージ', width=20)
    label_giftmsg.pack()
    label_giftmsg.place(x=20, y=10)

    text_giftmsg = tk.Text(frame_message, width=40, height=9)
    text_giftmsg.pack()
    text_giftmsg.place(x=20, y=30)
    text_giftmsg.insert("1.0", gift_message)

    # delivery message
    label_delivermsg = tk.Label(frame_message, text='配送メッセージ', width=20)
    label_delivermsg.pack()
    label_delivermsg.place(x=20, y=200)

    text_delivermsg = tk.Text(frame_message, width=40, height=9)
    text_delivermsg.pack()
    text_delivermsg.place(x=20, y=220)
    text_delivermsg.insert("1.0", delivery_message)



    # info save
    def handle_save():
        with codecs.open("Auto_Order_Info.txt", "w", "utf-8") as f:
            f.write('## ユーザー情報\n')

            if (entry_email.get() == ''):
                messagebox.showwarning('警告', 'メールは必須です。')
            f.write('USER_EMAIL=' + entry_email.get() + '\n')

            if (entry_password.get() == ''):
                messagebox.showwarning('警告', 'パスワードは必須です。')
            f.write('USER_PASSWORD=' + entry_password.get() + '\n')

            if (entry_appkey.get() == ''):
                messagebox.showwarning('警告', 'アプリキーは必須です。')
            f.write('APP_KEY=' + entry_appkey.get() + '\n')

            f.write('USER_STORE=' + ', oroshiuri-company' + '\n')

            if (entry_cardnumber.get() == ''):
                messagebox.showwarning('警告', 'カード番号は必須です。')
            f.write('CARD_NUMBER=' + entry_cardnumber.get() + '\n')
            
            if (entry_itemsender.get() == ''):
                messagebox.showwarning('警告', '発送人は必須です。')
            f.write('ITEM_SENDER=' + entry_itemsender.get() + '\n\n\n')


            f.write('##  1：一般アカウント、2：ビジネスアカウント\n')
            if (checked_var.get() == 0):
                f.write('USER_ACCOUNT_TYPE=' + '1' + '\n\n\n')
            elif (checked_var.get() == 1):
                f.write('USER_ACCOUNT_TYPE=' + '2' + '\n\n\n')


            f.write('## 置き配指定\n')
            f.write('# Basic account\n')
            f.write('#   1：玄関、2：宅配ボックス、3：ガスメーターボックス、4：自転車かご、5：車庫、6：建物内受付/管理人、7：置き配を利用しない、8：ご近所の方、9：設定なし\n')
            f.write('# Business account\n')
            f.write('#   1：建物内受付/管理人、2：玄関、3：宅配ボックス、4：ガスメーターボックス、5：車庫、6：自転車かご、7：置き配を利用しない\n\n')

            if (checked_var.get() == 1):
                opt = options2.index(selected_option.get())+1
            else :
                opt = options1.index(selected_option.get())+1
            f.write('PLACEMENT_SELECT=' + str(opt) + '\n\n\n')


            f.write('## ギフトメッセージ、配送メッセージ\n')
            if (text_giftmsg.get("1.0", tk.END) == ''):
                messagebox.showwarning('警告', 'ギフトメッセージは必須です。')
            f.write('GIFT_MESSAGE=' + text_giftmsg.get("1.0", tk.END))
            
            if (text_delivermsg.get("1.0", tk.END) == ''):
                messagebox.showwarning('警告', '発送メッセージは必須です。')
            f.write('DELIVERY_MESSAGE=' + text_delivermsg.get("1.0", tk.END) + '\n\n\n')


            # f.write('ORDER_CSV=' + entry_ordercsv.get() + '\n')
            if (file_path == ''):
                messagebox.showwarning('注文CSV')
            f.write('ORDER_CSV=' + file_path + '\n')
            f.write('OUTPUT_CSV=' + entry_outputcsv.get() + '\n')

    button_productcsv = tk.Button(frame_setting, text='保存', width=10, command=handle_save)
    button_productcsv.pack()
    button_productcsv.place(x=120, y=355)

    # info save and auto purchase
    def handle_autopurchase():
        handle_save()
        main()

    button_productcsv = tk.Button(frame_setting, text='自動購入', width=10, command=handle_autopurchase)
    button_productcsv.pack()
    button_productcsv.place(x=210, y=355)



    # waitting for auto purchase
    window.mainloop()

if __name__ == '__main__':
    draw_window()
