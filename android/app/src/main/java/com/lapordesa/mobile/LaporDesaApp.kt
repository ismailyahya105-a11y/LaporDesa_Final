package com.lapordesa.mobile

import android.app.Application
import com.lapordesa.mobile.data.TokenStore

class LaporDesaApp : Application() { lateinit var tokenStore: TokenStore; override fun onCreate() { super.onCreate(); tokenStore = TokenStore(this) } }
