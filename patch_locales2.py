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
        
    # Add genres to menu
    vi_data["admin"]["menu"]["genres"] = "Thể loại nhạc"
    en_data["admin"]["menu"]["genres"] = "Genres"
    
    # Check genres block
    if "genres" not in vi_data["admin"]:
        vi_data["admin"]["genres"] = {
            "title": "Quản lý Thể loại",
            "subtitle": "Quản lý các thể loại nhạc trên hệ thống",
            "add_new": "Thêm Thể loại Mới",
            "edit": "Sửa thể loại",
            "name": "Tên thể loại",
            "description": "Mô tả",
            "color": "Màu sắc",
            "delete_confirm": "Bạn có chắc chắn muốn xóa thể loại này? Các bài hát/album thuộc thể loại này sẽ bị mất liên kết."
        }
    else:
        vi_data["admin"]["genres"].update({
            "edit": "Sửa thể loại",
            "name": "Tên thể loại",
            "description": "Mô tả",
            "color": "Màu sắc",
            "delete_confirm": "Bạn có chắc chắn muốn xóa thể loại này? Các bài hát/album thuộc thể loại này sẽ bị mất liên kết."
        })
        
    if "genres" not in en_data["admin"]:
        en_data["admin"]["genres"] = {
            "title": "Genres Management",
            "subtitle": "Manage system genres for songs and albums",
            "add_new": "Add New Genre",
            "edit": "Edit Genre",
            "name": "Genre Name",
            "description": "Description",
            "color": "Color",
            "delete_confirm": "Are you sure you want to delete this genre? Songs/albums in this genre will lose this link."
        }
    else:
        en_data["admin"]["genres"].update({
            "edit": "Edit Genre",
            "name": "Genre Name",
            "description": "Description",
            "color": "Color",
            "delete_confirm": "Are you sure you want to delete this genre? Songs/albums in this genre will lose this link."
        })
        
    # Save
    with open(vi_path, "w", encoding="utf-8") as f:
        json.dump(vi_data, f, ensure_ascii=False, indent=2)
        
    with open(en_path, "w", encoding="utf-8") as f:
        json.dump(en_data, f, ensure_ascii=False, indent=2)
        
    print("Locales genres updated!")

if __name__ == "__main__":
    update_locales()
