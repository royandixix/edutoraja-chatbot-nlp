<?php
// Ambil ID user admin yang sedang login dan foto_url jika belum di-query
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['admin']) && !isset($current_admin_id)) {
    $current_username = $_SESSION['admin'];
    $stmt_curr = $conn->prepare("SELECT id, foto_url FROM users WHERE username = ?");
    $stmt_curr->bind_param("s", $current_username);
    $stmt_curr->execute();
    $curr_user_data = $stmt_curr->get_result()->fetch_assoc();
    if ($curr_user_data) {
        $current_admin_id = $curr_user_data['id'];
        $current_admin_foto = $curr_user_data['foto_url'];
    }
    $stmt_curr->close();
    
    // Daftar seluruh admin
    $admin_users = $conn->query("SELECT * FROM users ORDER BY id ASC");
}

// Function to render the profile modal
function renderProfileModal($current_admin_id, $current_admin_foto, $admin_users) {
    ?>
    <!-- Backdrop for Profile Modal -->
    <div class="modal-backdrop" id="profileBackdrop" onclick="closeProfileModal()"></div>

    <!-- Profile & Account Management Modal -->
    <div class="modal" id="profileModal" style="max-width: 600px;">
        <!-- Modal Tabs -->
        <div class="flex border-b border-outline-variant/30 mb-6">
            <button onclick="switchProfileTab('profile-info-tab', 'tab-btn-1')" id="tab-btn-1" class="flex-1 pb-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
                Profil & Keamanan
            </button>
            <button onclick="switchProfileTab('profile-users-tab', 'tab-btn-2')" id="tab-btn-2" class="flex-1 pb-3 text-sm font-bold border-b-2 border-transparent text-on-surface-variant/70 hover:text-on-surface transition-all">
                Manajemen Akun
            </button>
        </div>

        <!-- Tab 1: Profil & Keamanan -->
        <div id="profile-info-tab" class="profile-tab-content">
            <h3 class="font-headline text-xl font-bold mb-4 text-on-surface">Edit Profil Anda</h3>
            <form action="proses_akun.php?action=update_profile" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Foto Profil</label>
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-primary flex items-center justify-center text-white font-bold text-xl border">
                            <?php if (!empty($current_admin_foto) && file_exists("../" . $current_admin_foto)): ?>
                                <img src="../<?= htmlspecialchars($current_admin_foto) ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <input type="file" name="foto" accept="image/*" class="text-xs text-on-surface-variant border border-outline-variant/30 rounded-lg p-2 bg-surface-container-low focus:outline-none">
                            <p class="text-[10px] text-on-surface-variant mt-1">Format: JPG, JPEG, PNG, WEBP, atau GIF.</p>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Username Admin</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['admin']) ?>" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Password Baru (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeProfileModal()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Tab 2: Manajemen Akun -->
        <div id="profile-users-tab" class="profile-tab-content hidden">
            <h3 class="font-headline text-xl font-bold mb-4 text-on-surface">Manajemen Pengguna Admin</h3>
            
            <div class="max-h-60 overflow-y-auto border border-outline-variant/20 rounded-xl mb-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/30 text-xs font-bold text-on-surface-variant uppercase">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Username</th>
                            <th class="px-4 py-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        <?php 
                        if ($admin_users) {
                            $admin_users->data_seek(0);
                            while($user = $admin_users->fetch_assoc()): 
                            ?>
                            <tr class="hover:bg-surface-container-low/30 text-sm">
                                <td class="px-4 py-3 text-on-surface-variant">#<?= $user['id'] ?></td>
                                <td class="px-4 py-3 font-medium text-on-surface flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full overflow-hidden bg-primary flex items-center justify-center text-white font-bold text-xs border">
                                        <?php if (!empty($user['foto_url']) && file_exists("../" . $user['foto_url'])): ?>
                                            <img src="../<?= htmlspecialchars($user['foto_url']) ?>" alt="Avatar" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span>
                                        <?= htmlspecialchars($user['username']) ?>
                                        <?php if ($user['id'] == $current_admin_id): ?>
                                            <span class="text-[10px] bg-primary/10 text-primary font-bold px-2 py-0.5 rounded-full ml-1">Anda</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <?php if ($user['id'] != $current_admin_id): ?>
                                        <a href="proses_akun.php?action=delete_user&id=<?= $user['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin ini?');" class="text-xs font-bold text-error hover:underline ml-2">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                            endwhile;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeProfileModal()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Ganti Password User Lain (Nested / Helper Modal) -->
    <div class="modal-backdrop" id="otherPassBackdrop" style="z-index: 110;" onclick="closeOtherPassModal()"></div>
    <div class="modal" id="otherPassModal" style="z-index: 111; max-width: 400px;">
        <h3 class="font-headline text-lg font-bold mb-4 text-on-surface">Ubah Password Admin</h3>
        <p class="text-xs text-on-surface-variant mb-4">Mengubah password untuk akun: <strong id="other_pass_username"></strong></p>
        <form action="proses_akun.php?action=change_other_password" method="POST">
            <input type="hidden" name="user_id" id="other_pass_user_id">
            <div class="mb-6">
                <label class="block text-sm font-medium text-on-surface-variant mb-1">Password Baru</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeOtherPassModal()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Password</button>
            </div>
        </form>
    </div>

    <!-- Profile Modal JavaScript Functions -->
    <script>
        function openProfileModal() {
            document.getElementById('profileBackdrop').classList.add('active');
            document.getElementById('profileModal').classList.add('active');
        }

        function closeProfileModal() {
            document.getElementById('profileBackdrop').classList.remove('active');
            document.getElementById('profileModal').classList.remove('active');
        }

        // Pastikan tab switcher bekerja
        function switchProfileTab(tabId, btnId) {
            document.querySelectorAll('.profile-tab-content').forEach(el => {
                el.classList.add('hidden');
            });
            document.getElementById(tabId).classList.remove('hidden');

            document.getElementById('tab-btn-1').className = "flex-1 pb-3 text-sm font-bold border-b-2 border-transparent text-on-surface-variant/70 hover:text-on-surface transition-all";
            document.getElementById('tab-btn-2').className = "flex-1 pb-3 text-sm font-bold border-b-2 border-transparent text-on-surface-variant/70 hover:text-on-surface transition-all";

            document.getElementById(btnId).className = "flex-1 pb-3 text-sm font-bold border-b-2 border-primary text-primary transition-all";
        }

        function openChangeOtherPassModal(userId, username) {
            document.getElementById('other_pass_user_id').value = userId;
            document.getElementById('other_pass_username').innerText = username;
            document.getElementById('otherPassBackdrop').classList.add('active');
            document.getElementById('otherPassModal').classList.add('active');
        }

        function closeOtherPassModal() {
            document.getElementById('otherPassBackdrop').classList.remove('active');
            document.getElementById('otherPassModal').classList.remove('active');
        }
    </script>
    <?php
}
?>
