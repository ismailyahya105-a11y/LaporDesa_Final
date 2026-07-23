package com.lapordesa.mobile.ui

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.sp

val VillageGreen = Color(0xFF1B5E20)
val VillageGreenLight = Color(0xFFE8F5E9)
val Waiting = Color(0xFFF9A825)
val Processing = Color(0xFF1976D2)
val Complete = Color(0xFF2E7D32)
val AppSpacing = 16

@Composable
fun LaporDesaTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = lightColorScheme(
            primary = VillageGreen,
            onPrimary = Color.White,
            primaryContainer = VillageGreenLight,
            surface = Color(0xFFFFFBFE),
            background = Color(0xFFF7F9F6),
        ),
        typography = androidx.compose.material3.Typography(
            headlineSmall = TextStyle(fontSize = 24.sp, fontWeight = FontWeight.Bold),
            titleLarge = TextStyle(fontSize = 20.sp, fontWeight = FontWeight.Bold),
            titleMedium = TextStyle(fontSize = 16.sp, fontWeight = FontWeight.SemiBold),
            bodyLarge = TextStyle(fontSize = 16.sp),
            bodyMedium = TextStyle(fontSize = 14.sp),
        ),
        content = content,
    )
}
