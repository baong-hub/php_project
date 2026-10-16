<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gắn lớp học với tài khoản giảng viên chủ nhiệm.
     * Cột giao_vien được giữ lại làm tên hiển thị (tự đồng bộ theo tài khoản).
     */
    public function up(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->foreignId('giang_vien_id')->nullable()->after('ma_lop')
                ->constrained('users')->nullOnDelete();
            $table->string('giao_vien')->nullable()->change();
        });

        // Lớp cũ: gán giảng viên nếu tên giáo viên trùng tên một tài khoản giảng viên
        $giangViens = DB::table('users')->where('role', 'giang_vien')->pluck('id', 'name');

        foreach ($giangViens as $name => $id) {
            DB::table('lop_hocs')
                ->whereNull('giang_vien_id')
                ->where('giao_vien', $name)
                ->update(['giang_vien_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lop_hocs')->whereNull('giao_vien')->update(['giao_vien' => '']);

        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('giang_vien_id');
            $table->string('giao_vien')->nullable(false)->change();
        });
    }
};
