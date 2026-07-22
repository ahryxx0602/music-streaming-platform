import json
import sys
import os

def update_locales():
    locales_dir = "/home/vanthanh/Ind-Project/Laravel/music-streaming-platform/frontend/src/locales"
    vi_path = os.path.join(locales_dir, "vi.json")
    en_path = os.path.join(locales_dir, "en.json")
    
    with open(vi_path, "r", encoding="utf-8") as f:
        vi_data = json.load(f)
        
    with open(en_path, "r", encoding="utf-8") as f:
        en_data = json.load(f)
        
    if "users_page" not in vi_data["admin"]:
        vi_data["admin"]["users_page"] = {}
    if "form" not in vi_data["admin"]["users_page"]:
        vi_data["admin"]["users_page"]["form"] = {}
        
    if "users_page" not in en_data["admin"]:
        en_data["admin"]["users_page"] = {}
    if "form" not in en_data["admin"]["users_page"]:
        en_data["admin"]["users_page"]["form"] = {}
        
    # Updates for forms
    vi_updates = {
        "real_name": "Tên thật",
        "real_name_ph": "Ví dụ: Nguyễn Văn A",
        "stage_name": "Nghệ danh",
        "stage_name_ph": "Ví dụ: Sơn Tùng M-TP",
        "email": "Địa chỉ Email",
        "email_ph": "artist@domain.com",
        "password": "Mật khẩu",
        "password_ph": "Tối thiểu 8 ký tự, có ký tự đặc biệt",
        "password_conf": "Xác nhận mật khẩu",
        "password_conf_ph": "Nhập lại mật khẩu để xác minh",
        "role": "Vai trò (Role)",
        "role_ph": "Chọn một vai trò...",
        "plan": "Gói đăng ký",
        "plan_ph": "Chọn gói..."
    }
    
    en_updates = {
        "real_name": "Real Name",
        "real_name_ph": "E.g. John Doe",
        "stage_name": "Stage Name",
        "stage_name_ph": "E.g. The Weeknd",
        "email": "Email Address",
        "email_ph": "artist@domain.com",
        "password": "Password",
        "password_ph": "Min 8 chars, incl special characters",
        "password_conf": "Confirm Password",
        "password_conf_ph": "Re-enter password to verify",
        "role": "Role",
        "role_ph": "Select a role...",
        "plan": "Subscription Plan",
        "plan_ph": "Select plan..."
    }
    
    vi_data["admin"]["users_page"]["form"].update(vi_updates)
    en_data["admin"]["users_page"]["form"].update(en_updates)
    
    # Save
    with open(vi_path, "w", encoding="utf-8") as f:
        json.dump(vi_data, f, ensure_ascii=False, indent=2)
        
    with open(en_path, "w", encoding="utf-8") as f:
        json.dump(en_data, f, ensure_ascii=False, indent=2)
        
    print("Locales updated!")

if __name__ == "__main__":
    update_locales()
