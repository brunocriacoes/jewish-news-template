<?php
/**
 * Search and newsletter form matching the original layout.
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="sr-only" for="jnt-email-or-search"><?php esc_html_e('Pesquisar notícias', 'jewish-news-template'); ?></label>
  <input id="jnt-email-or-search" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('O seu e-mail', 'jewish-news-template'); ?>">
  <button type="submit"><?php esc_html_e('Subscrever', 'jewish-news-template'); ?> <span>→</span></button>
  <small><?php esc_html_e('Ao subscrever, concorda com a nossa política de privacidade.', 'jewish-news-template'); ?></small>
</form>