import tkinter as tk
import requests
from tkinter import messagebox
import json
from main import draw_window


window = tk.Tk()
window.geometry('280x150')
window.title('Amazon自動購入ツール キー')

label = tk.Label(window, text='アプリキーを入力してください。', fg='red')
label.pack()
label.place(x=40, y=20)

entry = tk.Entry(window, width=30)
entry.pack()
entry.place(x=40, y=50)

def validation():
    url = "https://xs767540.xsrv.jp/api/v1/tool_license_check"
    payload = 'key=' + entry.get()
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    }

    response = requests.request("POST", url, headers=headers, data=payload)
    decoded_data = json.loads(response.text)

    if (decoded_data['license'] == 'true'):
        window.destroy()
        draw_window()
    
    elif (decoded_data['license'] == 'false'):
        messagebox.showwarning("警告", "無効なキーです。もう一度お試しください！")

button = tk.Button(window, width=10, text='確認', command=validation)
button.pack()
button.place(x=150, y=80)

window.mainloop()
