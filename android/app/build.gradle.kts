import org.gradle.api.JavaVersion
import org.jetbrains.kotlin.gradle.dsl.JvmTarget

plugins { alias(libs.plugins.android.application); alias(libs.plugins.kotlin.android); alias(libs.plugins.kotlin.compose) }

android { namespace = "com.lapordesa.mobile"; compileSdk = 35
    defaultConfig { applicationId = "com.lapordesa.mobile"; minSdk = 24; targetSdk = 35; versionCode = 1; versionName = "1.0" }
    buildFeatures { compose = true; buildConfig = true }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
}

kotlin {
    compilerOptions {
        jvmTarget.set(JvmTarget.JVM_17)
    }
}

dependencies {
    implementation(libs.androidx.core.ktx); implementation(libs.androidx.lifecycle.runtime); implementation(libs.androidx.lifecycle.viewmodel)
    implementation(libs.androidx.activity.compose); implementation(platform(libs.androidx.compose.bom)); implementation(libs.androidx.compose.ui)
    implementation(libs.androidx.compose.ui.graphics); implementation(libs.androidx.compose.ui.tooling.preview); implementation(libs.androidx.compose.material3)
    implementation(libs.androidx.navigation); implementation(libs.retrofit); implementation(libs.retrofit.gson); implementation(libs.okhttp); implementation(libs.okhttp.logging)
    implementation(libs.datastore.preferences); implementation(libs.coil.compose)
    debugImplementation(libs.androidx.compose.ui.tooling)
}
