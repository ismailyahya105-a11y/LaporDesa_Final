package com.lapordesa.mobile.ui

import android.content.ContentResolver
import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.lapordesa.mobile.data.LaporanRepository
import com.lapordesa.mobile.data.TokenStore
import com.lapordesa.mobile.data.remote.Laporan
import com.lapordesa.mobile.data.remote.User
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch

data class UiState<T>(val loading:Boolean=false, val value:T?=null, val error:String?=null)
class AppViewModel(private val repo:LaporanRepository, private val tokens:TokenStore):ViewModel() {
 private val _authenticated=MutableStateFlow<Boolean?>(null); val authenticated=_authenticated.asStateFlow()
 private val _profile=MutableStateFlow(UiState<User>()); val profile=_profile.asStateFlow()
 private val _laporan=MutableStateFlow(UiState<List<Laporan>>()); val laporan=_laporan.asStateFlow()
 private val _detail=MutableStateFlow(UiState<Laporan>()); val detail=_detail.asStateFlow()
 init { viewModelScope.launch { _authenticated.value=!tokens.token.first().isNullOrBlank() } }
 fun login(email:String,password:String) = viewModelScope.launch { _profile.value=UiState(loading=true); repo.login(email,password).onSuccess { _authenticated.value=true }.onFailure { _profile.value=UiState(error=it.message ?: "Login gagal") } }
 fun loadHome()=viewModelScope.launch { _laporan.value=UiState(loading=true); runCatching { repo.laporan() }.onSuccess { _laporan.value=UiState(value=it) }.onFailure { _laporan.value=UiState(error=it.message) }; runCatching {repo.profile()}.onSuccess{_profile.value=UiState(value=it)} }
 fun loadDetail(id:Long)=viewModelScope.launch { _detail.value=UiState(loading=true); runCatching{repo.detail(id)}.onSuccess{_detail.value=UiState(value=it)}.onFailure{_detail.value=UiState(error=it.message)} }
 fun create(j:String,k:String,i:String,u:Uri?,r:ContentResolver,onDone:()->Unit)=viewModelScope.launch { _detail.value=UiState(loading=true); runCatching{repo.create(j,k,i,u,r)}.onSuccess{onDone()}.onFailure{_detail.value=UiState(error=it.message)} }
 fun logout(onDone:()->Unit)=viewModelScope.launch { repo.logout(); _authenticated.value=false; onDone() }
}
