package com.lapordesa.mobile.data.remote

import com.google.gson.GsonBuilder
import com.lapordesa.mobile.data.TokenStore
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.runBlocking
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object ApiClient {
 private const val BASE_URL = "http://10.0.2.2:8000/api/"
 fun create(tokens: TokenStore): ApiService {
  val auth = okhttp3.Interceptor { chain -> val token = runBlocking { tokens.token.first() }; chain.proceed(chain.request().newBuilder().header("Accept", "application/json").apply { if (!token.isNullOrBlank()) header("Authorization", "Bearer $token") }.build()) }
  val log = HttpLoggingInterceptor().apply { level = HttpLoggingInterceptor.Level.BASIC }
  return Retrofit.Builder().baseUrl(BASE_URL).client(OkHttpClient.Builder().addInterceptor(auth).addInterceptor(log).build()).addConverterFactory(GsonConverterFactory.create(GsonBuilder().create())).build().create(ApiService::class.java)
 }
}
