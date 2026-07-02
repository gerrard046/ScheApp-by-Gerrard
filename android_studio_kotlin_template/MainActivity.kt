package com.example.scheapp

import android.os.Bundle
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity

/**
 * ScheApp — WebView wrapper.
 *
 * URL server TIDAK di-hardcode: dibaca dari BuildConfig.BASE_URL yang
 * di-generate dari local.properties (lihat build.gradle.kts). Dengan begitu
 * tiap developer/perangkat bisa punya URL sendiri tanpa mengubah kode:
 *   - Emulator : scheapp.baseUrl=http://10.0.2.2:8000
 *   - HP fisik : scheapp.baseUrl=http://192.168.x.x:8000 (IP laptop, cek ipconfig)
 */
class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val baseUrl = resolveBaseUrl()

        webView = WebView(this)

        val settings: WebSettings = webView.settings
        settings.javaScriptEnabled = true      // wajib: app pakai Alpine.js
        settings.domStorageEnabled = true      // wajib: session & localStorage
        settings.loadWithOverviewMode = true
        settings.useWideViewPort = true
        settings.builtInZoomControls = false
        settings.displayZoomControls = false

        // Link tetap terbuka di dalam aplikasi, bukan lompat ke Chrome
        webView.webViewClient = WebViewClient()

        setContentView(webView)
        webView.loadUrl(baseUrl)
    }

    /**
     * Ambil URL dari BuildConfig; kalau developer lupa mengisi
     * local.properties, langsung gagal dengan pesan yang jelas —
     * lebih baik crash informatif daripada layar putih misterius.
     */
    private fun resolveBaseUrl(): String {
        val url = BuildConfig.BASE_URL
        check(url.isNotBlank()) {
            """
            BASE_URL belum dikonfigurasi!

            Buka file local.properties di root project, tambahkan:
                scheapp.baseUrl=http://10.0.2.2:8000

            (Contoh lengkap ada di local.properties.example)
            Lalu Sync Project with Gradle Files dan jalankan ulang.
            """.trimIndent()
        }
        return url
    }

    @Deprecated("Deprecated in Java")
    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }
}
