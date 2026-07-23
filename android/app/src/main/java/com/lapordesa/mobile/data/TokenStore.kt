package com.lapordesa.mobile.data

import android.content.Context
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore by preferencesDataStore("auth_preferences")
class TokenStore(private val context: Context) {
    private val tokenKey = stringPreferencesKey("sanctum_token")
    val token: Flow<String?> = context.dataStore.data.map { it[tokenKey] }
    suspend fun save(value: String) = context.dataStore.edit { it[tokenKey] = value }
    suspend fun clear() = context.dataStore.edit { it.remove(tokenKey) }
}
