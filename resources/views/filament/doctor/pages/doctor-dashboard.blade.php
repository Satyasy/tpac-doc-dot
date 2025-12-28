<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                        <x-heroicon-o-user-circle class="h-10 w-10 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-950 dark:text-white">
                            Selamat Datang, Dr. {{ $user->name }}!
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Dashboard Dokter DocDot - Partner Konsultasi Kesehatan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <!-- Chatbot Partner -->
            <div
                class="fi-section rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 shadow-sm ring-1 ring-gray-950/5 dark:from-blue-900/20 dark:to-cyan-900/20 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-800">
                            <x-heroicon-o-chat-bubble-left-right class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-950 dark:text-white">Chatbot Partner</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Akses chatbot AI untuk analisis medis dan riset
                            </p>
                            <a href="/consultation"
                                class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                Buka Konsultasi
                                <x-heroicon-m-arrow-right class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referensi Obat -->
            <div
                class="fi-section rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 shadow-sm ring-1 ring-gray-950/5 dark:from-green-900/20 dark:to-emerald-900/20 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-800">
                            <x-heroicon-o-beaker class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-950 dark:text-white">Katalog Obat</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Referensi obat, dosis, dan interaksi
                            </p>
                            <a href="/drug-catalog"
                                class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400">
                                Lihat Katalog
                                <x-heroicon-m-arrow-right class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Artikel Kesehatan -->
            <div
                class="fi-section rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 shadow-sm ring-1 ring-gray-950/5 dark:from-purple-900/20 dark:to-pink-900/20 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-800">
                            <x-heroicon-o-document-text class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-950 dark:text-white">Artikel Medis</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Artikel dan jurnal kesehatan terkini
                            </p>
                            <a href="/articles"
                                class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400">
                                Baca Artikel
                                <x-heroicon-m-arrow-right class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Features Info -->
        <div
            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    Fitur Chatbot untuk Dokter
                </h3>
            </div>
            <div class="fi-section-content p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-magnifying-glass-circle class="h-6 w-6 text-primary-500" />
                        <div>
                            <h4 class="font-medium text-gray-950 dark:text-white">Analisis Penyakit Mendalam</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Analisis gejala dan differential
                                diagnosis</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-beaker class="h-6 w-6 text-primary-500" />
                        <div>
                            <h4 class="font-medium text-gray-950 dark:text-white">Farmakologi & Interaksi Obat</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Informasi obat dan interaksi antar obat
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-primary-500" />
                        <div>
                            <h4 class="font-medium text-gray-950 dark:text-white">Guideline Klinis</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ringkasan pedoman klinis terkini</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-academic-cap class="h-6 w-6 text-primary-500" />
                        <div>
                            <h4 class="font-medium text-gray-950 dark:text-white">Insight Berbasis Riset</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Informasi dari jurnal dan riset medis
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
