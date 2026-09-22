<?php
declare(strict_types=1);
function jnt_setup(): void { add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('html5', ['search-form','style','script']); register_nav_menus(['primary'=>__('Navegação principal','jewish-news-template'),'footer_sections'=>__('Rodapé: secções','jewish-news-template'),'footer_contact'=>__('Rodapé: contacto','jewish-news-template')]); }
add_action('after_setup_theme','jnt_setup');
function jnt_assets(): void { wp_enqueue_style('jnt-style',get_stylesheet_uri(),[], (string) filemtime(get_stylesheet_directory() . '/style.css')); wp_enqueue_script('jnt-theme',get_theme_file_uri('/assets/js/theme.js'),[], (string) filemtime(get_theme_file_path('/assets/js/theme.js')),true); }
add_action('wp_enqueue_scripts','jnt_assets');
function jnt_fallback_menu(): void { echo '<ul class="menu">'; wp_list_pages(['title_li'=>'']); echo '</ul>'; }
function jnt_category(): string { $categories=get_the_category(); return $categories ? $categories[0]->name : __('Notícias','jewish-news-template'); }
function jnt_story_image(string $size = 'large', string $fallback = ''): void { if (has_post_thumbnail()) { the_post_thumbnail($size, ['loading' => 'eager']); return; } if ($fallback) { printf('<img src="%s" alt="" loading="eager">', esc_url($fallback)); } }

