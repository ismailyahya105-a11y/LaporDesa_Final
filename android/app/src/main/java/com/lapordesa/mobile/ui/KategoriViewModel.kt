package com.lapordesa.mobile.ui

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.lapordesa.mobile.data.KategoriRepository
import com.lapordesa.mobile.data.remote.Kategori
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class KategoriUiState(
    val loading: Boolean = false,
    val kategoriList: List<Kategori> = emptyList(),
    val error: String? = null,
)

class KategoriViewModel(private val repository: KategoriRepository) : ViewModel() {
    private val _state = MutableStateFlow(KategoriUiState())
    val state = _state.asStateFlow()

    fun loadKategori() {
        if (_state.value.loading || _state.value.kategoriList.isNotEmpty()) return
        viewModelScope.launch {
            _state.value = KategoriUiState(loading = true)
            runCatching { repository.getKategori() }
                .onSuccess { _state.value = KategoriUiState(kategoriList = it) }
                .onFailure { _state.value = KategoriUiState(error = it.message ?: "Gagal mengambil kategori") }
        }
    }
}
