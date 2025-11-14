<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->comment('轮播图标题');
            $table->string('description', 255)->nullable()->comment('轮播图描述');
            $table->string('link', 255)->nullable()->comment('轮播图链接');
            $table->string('background', 255)->default('linear-gradient(135deg, #667eea 0%, #764ba2 100%)')->comment('轮播图背景（颜色、渐变或图片URL）');
            $table->integer('sort_order')->default(0)->comment('排序顺序');
            $table->boolean('active')->default(true)->comment('是否激活');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            
            // 添加索引
            $table->index('sort_order');
            $table->index('active');
        });
        
        // 插入默认数据
        $this->insertDefaultData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
    
    /**
     * 插入默认轮播图数据
     */
    private function insertDefaultData()
    {
        $now = Carbon::now();
        $defaultCarousels = [
            [
                'title' => '欢迎来到PT站',
                'description' => '享受高速下载体验',
                'link' => '#',
                'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'sort_order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => '最新热门种子',
                'description' => '每周更新精选内容',
                'link' => '#',
                'background' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'sort_order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => '邀请好友',
                'description' => '',
                'link' => '#',
                'background' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'sort_order' => 3,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        
        foreach ($defaultCarousels as $carousel) {
            DB::table('carousels')->insert($carousel);
        }
    }
};