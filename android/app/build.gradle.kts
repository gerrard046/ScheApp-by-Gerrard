import java.util.Properties

plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
}

// ============================================================
// URL server dibaca dari local.properties (tidak ikut ke git).
//   scheapp.baseUrl=http://10.0.2.2:8000
// Kalau kosong: build debug fallback ke http://10.0.2.2:8000
// (emulator), build release WAJIB diisi (app menolak jalan).
// ============================================================
val localProps = Properties().apply {
    val file = rootProject.file("local.properties")
    if (file.exists()) {
        file.inputStream().use { load(it) }
    }
}
val scheappBaseUrl: String = localProps.getProperty("scheapp.baseUrl") ?: ""

android {
    namespace = "com.gerrard.scheapp"
    compileSdk = 35

    defaultConfig {
        applicationId = "com.gerrard.scheapp"
        minSdk = 24
        targetSdk = 35
        versionCode = 1
        versionName = "1.0"

        buildConfigField("String", "BASE_URL", "\"$scheappBaseUrl\"")
    }

    buildFeatures {
        buildConfig = true
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    kotlinOptions {
        jvmTarget = "17"
    }
}

dependencies {
    implementation("androidx.core:core-ktx:1.13.1")
    implementation("androidx.appcompat:appcompat:1.7.0")
    implementation("com.google.android.material:material:1.12.0")
}
