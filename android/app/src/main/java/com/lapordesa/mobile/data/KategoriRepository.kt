package com.lapordesa.mobile.data

import com.lapordesa.mobile.data.remote.ApiService
import com.lapordesa.mobile.data.remote.Kategori

class KategoriRepository(private val api: ApiService) {
    suspend fun getKategori(): List<Kategori> {
        val response = api.getKategori()
        if (!response.success) error(response.message ?: "Gagal mengambil kategori")
        return response.data.orEmpty()
    }
}
