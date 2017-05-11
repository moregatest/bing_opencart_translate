# bing_opencart_translate
Opencart **cking translate model by linbay

# Usage
```
php bin.php --help
```
Then you will get help
```
php bin.php --help -s en-gb -t zh-TW
```

他會偵測 org目錄下的 en-gb 啥小的目錄 並比對同一目錄下的 zh-TW 目錄（maybe 是舊版 當然英文永遠是最新的）
當zh-TW目錄下的相同檔案 沒有某個key值的對照 程式就會自動送出給Bing翻譯

# TODO
1.對內容有html的部份 另外處理
2.支援po檔輸出


