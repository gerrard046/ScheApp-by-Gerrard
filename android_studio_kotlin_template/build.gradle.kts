import java.util.Properties

plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
}

// ============================================================
// Baca URL server dari local.properties (TIDAK ikut ke git).
// Contoh isi local.properties:
//   scheapp.baseUrl=http://10.0.2.2:8000
// Lihat local.properties.example untuk template lengkap.
// ============================================================
val localProps = Properties().apply {
    val file = rootProject.file("local.properties")
    if (file.exists()) {
        file.inputStream().use { load(it) }
    }
}
val scheappBaseUrl: String = localProps.getProperty("scheapp.baseUrl") ?: ""

android {
    namespace = "com.example.scheapp"
    compileSdk = 34

    defaultConfig {
        applicationId = "com.example.scheapp"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0"

        // Expose ke BuildConfig.BASE_URL — bisa dibaca dari kode Kotlin
        buildConfigField("String", "BASE_URL", "\"$scheappBaseUrl\"")

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
    }

    buildFeatures {
        // Wajib true agar buildConfigField di atas di-generate (AGP 8+ defaultnya mati)
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
        sourceCompatibility = JavaVersion.VERSION_1_8
        targetCompatibility = JavaVersion.VERSION_1_8
    }
    kotlinOptions {
        jvmTarget = "1.8"
    }
}

dependencies {
    implementation("androidx.core:core-ktx:1.12.0")
    implementation("androidx.appcompat:appcompat:1.6.1")
    implementation("com.google.android.material:material:1.11.0")
    implementation("androidx.constraintlayout:constraintlayout:2.1.4")
}
