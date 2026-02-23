package com.example.scheapp

import android.os.Bundle
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.ComponentActivity

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        // Membuat WebView secara dinamis
        val webView = WebView(this)
        
        val settings: WebSettings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.loadWithOverviewMode = true
        settings.useWideViewPort = true
        settings.builtInZoomControls = false
        settings.displayZoomControls = false
        
        // Agar link tetap terbuka di dalam aplikasi
        webView.webViewClient = WebViewClient()

        setContentView(webView)

        // GANTI DENGAN IP LAPTOP KONTROLER JIKA PAKAI HP ASLI
        // Jika pakai emulator, gunakan http://10.0.2.2:8000/schedules
        webView.loadUrl("http://10.0.2.2:8000/schedules")
    }

    // Menangani tombol back agar kembali ke halaman sebelumnya di web, bukan keluar app
    override fun onBackPressed() {
        val webView = contentView as? WebView
        if (webView?.canGoBack() == true) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }
}
