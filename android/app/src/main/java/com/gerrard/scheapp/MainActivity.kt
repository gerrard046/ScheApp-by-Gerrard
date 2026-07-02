package com.gerrard.scheapp

import android.annotation.SuppressLint
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.webkit.CookieManager
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity

/**
 * ScheApp — WebView wrapper untuk aplikasi Laravel.
 *
 * URL server dibaca dari BuildConfig.BASE_URL (di-generate dari
 * local.properties, lihat app/build.gradle.kts):
 *   - Emulator : http://10.0.2.2:8000 (fallback otomatis saat debug)
 *   - HP fisik : isi scheapp.baseUrl=http://IP_LAPTOP:8000
 */
class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private var baseHost: String = ""

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val baseUrl = resolveBaseUrl()
        baseHost = Uri.parse(baseUrl).host ?: ""

        webView = WebView(this)
        with(webView.settings) {
            javaScriptEnabled = true        // wajib: UI pakai Alpine.js
            domStorageEnabled = true        // wajib: session & localStorage
            useWideViewPort = true
            loadWithOverviewMode = true
            builtInZoomControls = false
            displayZoomControls = false
            allowFileAccess = false         // keamanan: JS tak boleh baca file device
            allowContentAccess = false
        }

        // Cookie session Laravel (CSRF + login) harus diterima dan bertahan.
        CookieManager.getInstance().apply {
            setAcceptCookie(true)
            setAcceptThirdPartyCookies(webView, false)
        }

        webView.webViewClient = ScheAppWebViewClient()
        setContentView(webView)
        webView.loadUrl(baseUrl)
    }

    /**
     * Debug build tanpa konfigurasi -> fallback ke emulator (10.0.2.2).
     * Release build WAJIB mengisi scheapp.baseUrl — kalau kosong,
     * langsung gagal dengan pesan yang jelas.
     */
    private fun resolveBaseUrl(): String {
        if (BuildConfig.BASE_URL.isNotBlank()) return BuildConfig.BASE_URL
        if (BuildConfig.DEBUG) return "http://10.0.2.2:8000"
        error(
            "scheapp.baseUrl belum diisi di local.properties. " +
            "Build release wajib menunjuk server production (https)."
        )
    }

    inner class ScheAppWebViewClient : WebViewClient() {

        override fun shouldOverrideUrlLoading(
            view: WebView,
            request: WebResourceRequest,
        ): Boolean {
            val url = request.url
            val host = url.host ?: return false

            // Server sendiri + halaman login Google (OAuth) tetap di dalam app,
            // supaya redirect OAuth kembali mulus ke session yang sama.
            val allowedInApp = host == baseHost ||
                host == "accounts.google.com" ||
                (host.endsWith(".google.com") && url.path?.startsWith("/o/oauth2") == true)

            if (allowedInApp) return false

            // Link keluar (YouTube, dokumen, dsb.) dibuka di browser eksternal.
            return try {
                startActivity(Intent(Intent.ACTION_VIEW, url))
                true
            } catch (_: Exception) {
                false
            }
        }

        override fun onReceivedError(
            view: WebView,
            request: WebResourceRequest,
            error: WebResourceError,
        ) {
            // Hanya ganti halaman kalau frame utama yang gagal
            // (bukan gambar/asset kecil yang error).
            if (request.isForMainFrame) {
                view.loadUrl("file:///android_asset/error.html")
            }
        }
    }

    override fun onPause() {
        super.onPause()
        // Pastikan cookie session tersimpan ke disk saat app di-background.
        CookieManager.getInstance().flush()
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
