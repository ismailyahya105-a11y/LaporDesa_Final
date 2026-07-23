package com.lapordesa.mobile

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewmodel.compose.viewModel
import com.lapordesa.mobile.data.KategoriRepository
import com.lapordesa.mobile.data.LaporanRepository
import com.lapordesa.mobile.data.remote.ApiClient
import com.lapordesa.mobile.ui.AppViewModel
import com.lapordesa.mobile.ui.KategoriViewModel
import com.lapordesa.mobile.ui.LaporDesaApp
import com.lapordesa.mobile.ui.LaporDesaTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val application = application as LaporDesaApp
        val api = ApiClient.create(application.tokenStore)
        val laporanRepository = LaporanRepository(api, application.tokenStore)
        val kategoriRepository = KategoriRepository(api)

        setContent {
            val appViewModel: AppViewModel = viewModel(
                factory = object : ViewModelProvider.Factory {
                    override fun <T : ViewModel> create(modelClass: Class<T>): T {
                        @Suppress("UNCHECKED_CAST")
                        return AppViewModel(laporanRepository, application.tokenStore) as T
                    }
                },
            )
            val kategoriViewModel: KategoriViewModel = viewModel(
                factory = object : ViewModelProvider.Factory {
                    override fun <T : ViewModel> create(modelClass: Class<T>): T {
                        @Suppress("UNCHECKED_CAST")
                        return KategoriViewModel(kategoriRepository) as T
                    }
                },
            )

            LaporDesaTheme {
                LaporDesaApp(appViewModel, kategoriViewModel)
            }
        }
    }
}
