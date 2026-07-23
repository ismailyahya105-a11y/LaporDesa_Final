package com.lapordesa.mobile.ui

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import coil.compose.AsyncImage
import com.lapordesa.mobile.data.remote.Kategori
import com.lapordesa.mobile.data.remote.Laporan

private val cardShape = RoundedCornerShape(20.dp)

@Composable
fun LaporDesaApp(vm: AppViewModel, kategoriVm: KategoriViewModel) {
    val nav = rememberNavController()
    val authenticated by vm.authenticated.collectAsState()
    when (authenticated) {
        null -> LoadingScreen()
        false -> LoginScreen(vm)
        true -> NavHost(navController = nav, startDestination = "dashboard") {
            composable("dashboard") { DashboardScreen(vm, { nav.navigate("create") }, { nav.navigate("detail/$it") }, { nav.navigate("profile") }) }
            composable("create") { CreateLaporanScreen(vm, kategoriVm) { nav.popBackStack(); vm.loadHome() } }
            composable("detail/{id}") { entry -> DetailLaporanScreen(vm, entry.arguments?.getString("id")?.toLongOrNull() ?: 0L) { nav.popBackStack() } }
            composable("profile") { ProfileScreen(vm) { nav.navigate("dashboard") { popUpTo("dashboard") { inclusive = true } } } }
        }
    }
}

@Composable
private fun LoginScreen(vm: AppViewModel) {
    var email by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }
    var visible by rememberSaveable { mutableStateOf(false) }
    val state by vm.profile.collectAsState()
    val snackbar = remember { SnackbarHostState() }
    LaunchedEffect(state.error) { state.error?.let { snackbar.showSnackbar("Gagal login, periksa email dan password") } }
    Scaffold(snackbarHost = { SnackbarHost(snackbar) }) { padding ->
        Column(Modifier.fillMaxSize().padding(padding).padding(24.dp), verticalArrangement = Arrangement.Center) {
            Text("Lapor Desa", style = MaterialTheme.typography.headlineSmall, color = VillageGreen)
            Text("Sampaikan laporan, pantau tindak lanjut", color = MaterialTheme.colorScheme.onSurfaceVariant)
            Spacer(Modifier.height(28.dp))
            RoundedField(email, { email = it }, "Email")
            Spacer(Modifier.height(12.dp))
            RoundedField(password, { password = it }, "Password", if (visible) VisualTransformation.None else PasswordVisualTransformation(), trailing = { TextButton(onClick = { visible = !visible }) { Text(if (visible) "Sembunyi" else "Lihat") } })
            Spacer(Modifier.height(20.dp))
            Button(onClick = { vm.login(email, password) }, modifier = Modifier.fillMaxWidth().height(52.dp), enabled = email.isNotBlank() && password.isNotBlank() && !state.loading) { if (state.loading) CircularProgressIndicator(Modifier.size(22.dp), color = Color.White) else Text("Masuk") }
        }
    }
}

@Composable
private fun RoundedField(value: String, onValueChange: (String) -> Unit, label: String, visualTransformation: VisualTransformation = VisualTransformation.None, trailing: @Composable (() -> Unit)? = null, isError: Boolean = false) {
    OutlinedTextField(value, onValueChange, Modifier.fillMaxWidth(), label = { Text(label) }, singleLine = true, shape = RoundedCornerShape(16.dp), visualTransformation = visualTransformation, trailingIcon = trailing, isError = isError)
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun DashboardScreen(vm: AppViewModel, onCreate: () -> Unit, onDetail: (Long) -> Unit, onProfile: () -> Unit) {
    val reports by vm.laporan.collectAsState()
    val profile by vm.profile.collectAsState()
    LaunchedEffect(Unit) { vm.loadHome() }
    Scaffold(topBar = { TopAppBar(title = { Text("Lapor Desa") }, actions = { TextButton(onClick = onProfile) { Text("Profil") } }) }, floatingActionButton = { ExtendedFloatingActionButton(onClick = onCreate, containerColor = VillageGreen) { Text("+ Buat Laporan") } }) { padding ->
        when {
            reports.loading -> LoadingScreen(Modifier.padding(padding))
            reports.error != null -> ErrorState(Modifier.padding(padding)) { vm.loadHome() }
            else -> LazyColumn(Modifier.fillMaxSize().padding(padding).padding(horizontal = 16.dp), verticalArrangement = Arrangement.spacedBy(12.dp), contentPadding = PaddingValues(vertical = 12.dp)) {
                item { Text("Halo, ${profile.value?.name ?: "Warga"}", style = MaterialTheme.typography.headlineSmall) }
                item { Text("Laporan terbaru", style = MaterialTheme.typography.titleLarge) }
                if (reports.value.isNullOrEmpty()) item { EmptyState() } else items(reports.value.orEmpty(), key = { it.id }) { report -> ReportCard(report) { onDetail(report.id) } }
            }
        }
    }
}

@Composable
private fun ReportCard(report: Laporan, onClick: () -> Unit) {
    Card(Modifier.fillMaxWidth().clickable(onClick = onClick), shape = cardShape) {
        Row(Modifier.padding(14.dp), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            report.fotoUrl?.let { AsyncImage(emulatorUrl(it), "Foto laporan", Modifier.size(72.dp).clip(RoundedCornerShape(12.dp)), contentScale = ContentScale.Crop) }
            Column(Modifier.weight(1f)) { Text(report.judul, style = MaterialTheme.typography.titleMedium); Text(report.kategori?.nama ?: "Kategori", color = VillageGreen); Text(report.status, color = MaterialTheme.colorScheme.onSurfaceVariant); Text(formatDate(report.tanggal), color = MaterialTheme.colorScheme.onSurfaceVariant) }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun CreateLaporanScreen(vm: AppViewModel, kategoriVm: KategoriViewModel, onDone: () -> Unit) {
    val context = LocalContext.current
    var judul by rememberSaveable { mutableStateOf("") }
    var isi by rememberSaveable { mutableStateOf("") }
    var selected by remember { mutableStateOf<Kategori?>(null) }
    var expanded by remember { mutableStateOf(false) }
    var photo by remember { mutableStateOf<Uri?>(null) }
    var submitted by rememberSaveable { mutableStateOf(false) }
    val reportState by vm.detail.collectAsState()
    val kategoriState by kategoriVm.state.collectAsState()
    val picker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { photo = it }
    LaunchedEffect(Unit) { kategoriVm.loadKategori() }
    Scaffold(topBar = { TopAppBar(title = { Text("Buat Laporan") }) }) { padding ->
        LazyColumn(Modifier.padding(padding).padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
            item { RoundedField(judul, { judul = it }, "Judul laporan", isError = submitted && judul.isBlank()) }
            item {
                ExposedDropdownMenuBox(expanded = expanded, onExpandedChange = { expanded = !expanded }) {
                    OutlinedTextField(selected?.nama ?: "Pilih kategori", {}, Modifier.menuAnchor().fillMaxWidth(), readOnly = true, label = { Text("Kategori") }, trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) }, isError = submitted && selected == null)
                    ExposedDropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) { kategoriState.kategoriList.forEach { option -> DropdownMenuItem(text = { Text(option.nama) }, onClick = { selected = option; expanded = false }) } }
                }
                kategoriState.error?.let { FieldError("Kategori gagal dimuat") }
            }
            item { OutlinedTextField(isi, { isi = it }, Modifier.fillMaxWidth().heightIn(min = 150.dp), label = { Text("Isi laporan") }, minLines = 5, isError = submitted && isi.isBlank()) }
            item { OutlinedButton(onClick = { picker.launch("image/*") }, modifier = Modifier.fillMaxWidth(), border = BorderStroke(1.dp, VillageGreen)) { Text("Tambah Foto") }; photo?.let { AsyncImage(it, "Preview foto", Modifier.fillMaxWidth().height(190.dp).clip(cardShape), contentScale = ContentScale.Crop) } }
            item {
                reportState.error?.let { ErrorCard(it) }
                Button(onClick = { submitted = true; selected?.takeIf { judul.isNotBlank() && isi.isNotBlank() }?.let { vm.create(judul, it.id.toString(), isi, photo, context.contentResolver, onDone) } }, modifier = Modifier.fillMaxWidth().height(52.dp), enabled = !reportState.loading) { if (reportState.loading) CircularProgressIndicator(Modifier.size(22.dp), color = Color.White) else Text("Kirim Laporan") }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun DetailLaporanScreen(vm: AppViewModel, id: Long, back: () -> Unit) {
    val state by vm.detail.collectAsState()
    LaunchedEffect(id) { vm.loadDetail(id) }
    Scaffold(topBar = { TopAppBar(title = { Text("Detail Laporan") }, navigationIcon = { TextButton(onClick = back) { Text("Kembali") } }) }) { padding ->
        val report = state.value
        when { state.loading -> LoadingScreen(Modifier.padding(padding)); state.error != null || report == null -> ErrorState(Modifier.padding(padding)) { vm.loadDetail(id) }; else -> LazyColumn(Modifier.padding(padding).padding(16.dp), verticalArrangement = Arrangement.spacedBy(14.dp)) { item { report.fotoUrl?.let { AsyncImage(emulatorUrl(it), "Foto laporan", Modifier.fillMaxWidth().height(245.dp).clip(cardShape), contentScale = ContentScale.Crop) }; Text(report.judul, style = MaterialTheme.typography.headlineSmall); Text(report.kategori?.nama ?: "-", color = VillageGreen); Text(report.status); HorizontalDivider(); Text("Isi laporan", style = MaterialTheme.typography.titleMedium); Text(report.isiLaporan); Text("Tanggal laporan", style = MaterialTheme.typography.titleMedium); Text(formatDate(report.tanggal)) } } }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ProfileScreen(vm: AppViewModel, onLogout: () -> Unit) {
    val profile by vm.profile.collectAsState()
    Scaffold(topBar = { TopAppBar(title = { Text("Profil") }) }) { padding -> Column(Modifier.fillMaxSize().padding(padding).padding(20.dp)) { Text(profile.value?.name.orEmpty(), style = MaterialTheme.typography.headlineSmall); Text(profile.value?.email.orEmpty()); Spacer(Modifier.weight(1f)); OutlinedButton(onClick = { vm.logout(onLogout) }, modifier = Modifier.fillMaxWidth()) { Text("Logout") } } }
}

@Composable private fun LoadingScreen(modifier: Modifier = Modifier) = Box(modifier.fillMaxSize(), contentAlignment = Alignment.Center) { CircularProgressIndicator(color = VillageGreen) }
@Composable private fun EmptyState() = Card(Modifier.fillMaxWidth(), shape = cardShape) { Text("Belum ada laporan", modifier = Modifier.padding(24.dp)) }
@Composable private fun FieldError(message: String) = Text(message, color = MaterialTheme.colorScheme.error)
@Composable private fun ErrorState(modifier: Modifier, retry: () -> Unit) = Box(modifier.fillMaxSize(), contentAlignment = Alignment.Center) { ErrorCard("Tidak dapat mengambil data", retry) }
@Composable private fun ErrorCard(message: String, retry: (() -> Unit)? = null) = Card(Modifier.padding(16.dp), colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.errorContainer)) { Column(Modifier.padding(16.dp)) { Text(message); retry?.let { TextButton(onClick = it) { Text("Coba Lagi") } } } }
private fun formatDate(value: String?): String = value?.replace("T", " ")?.substringBefore(".") ?: "-"
private fun emulatorUrl(url: String): String = url.replace("://127.0.0.1", "://10.0.2.2").replace("://localhost", "://10.0.2.2")
