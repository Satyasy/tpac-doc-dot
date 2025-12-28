<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\HealthArticle;
use App\Models\Drug;
use App\Models\AiAuditLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
   public static function getSort(): int
   {
      return 1;
   }

   protected function getStats(): array
   {
      $totalUsers = User::count();
      $newUsersThisMonth = User::whereMonth('created_at', now()->month)
         ->whereYear('created_at', now()->year)
         ->count();

      $totalSessions = ChatSession::count();
      $totalMessages = ChatMessage::count();

      $totalArticles = HealthArticle::whereNotNull('published_at')->count();
      $totalDrugs = Drug::count();

      $aiQueries = AiAuditLog::count();
      $blockedQueries = AiAuditLog::where('blocked', true)->count();

      return [
         Stat::make('Total Pengguna', $totalUsers)
            ->description("$newUsersThisMonth baru bulan ini")
            ->descriptionIcon('heroicon-m-user-plus')
            ->color('success')
            ->chart([7, 3, 4, 5, 6, $newUsersThisMonth]),

         Stat::make('Total Sesi Chat', $totalSessions)
            ->description("$totalMessages total pesan")
            ->descriptionIcon('heroicon-m-chat-bubble-left-right')
            ->color('info'),

         Stat::make('Artikel Kesehatan', $totalArticles)
            ->description('Artikel terpublikasi')
            ->descriptionIcon('heroicon-m-document-text')
            ->color('warning'),

         Stat::make('Katalog Obat', $totalDrugs)
            ->description('Total obat tersedia')
            ->descriptionIcon('heroicon-m-beaker')
            ->color('primary'),

         Stat::make('AI Queries', $aiQueries)
            ->description("$blockedQueries query diblokir")
            ->descriptionIcon('heroicon-m-cpu-chip')
            ->color($blockedQueries > 0 ? 'danger' : 'success'),
      ];
   }
}
