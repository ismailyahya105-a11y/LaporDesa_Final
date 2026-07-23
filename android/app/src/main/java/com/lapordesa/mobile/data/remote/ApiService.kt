package com.lapordesa.mobile.data.remote

import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.POST
import retrofit2.http.Part
import retrofit2.http.Path

interface ApiService {
 @POST("login") suspend fun login(@Body request: LoginRequest): LoginResponse
 @POST("logout") suspend fun logout(): ApiResponse<Unit>
 @GET("profile") suspend fun profile(): ProfileResponse
 @GET("kategori") suspend fun getKategori(): ApiResponse<List<Kategori>>
 @GET("laporan") suspend fun laporan(): ApiResponse<LaporanIndexPayload>
 @GET("laporan/{id}") suspend fun detail(@Path("id") id: Long): ApiResponse<Laporan>
 @Multipart @POST("laporan") suspend fun createLaporan(@Part("judul") judul: RequestBody, @Part("kategori_id") kategoriId: RequestBody, @Part("isi_laporan") isi: RequestBody, @Part foto: MultipartBody.Part?): ApiResponse<Laporan>
}
