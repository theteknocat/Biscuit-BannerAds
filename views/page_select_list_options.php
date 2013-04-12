<?php
if (!empty($pages[$current_parent_id])) {
	foreach ($pages[$current_parent_id] as $page) {
		if ($page->access_level() == PUBLIC_USER) {
			$slug_bits = explode('/',$page->slug());
			$indent = count($slug_bits)-1;
			$indent_str = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$indent);
			$page_title = $page->title();
			$page_title = $indent_str.addslashes(H::purify_text($page->title()));
			?><option value="<?php echo $page->url() ?>"<?php if ($page->id() == $selected_parent) { ?> selected="selected"<?php } ?>><?php echo $page_title; ?></option><?php
			if (!empty($pages[$page->id()]) && $with_children) {
				echo $Navigation->render_pages_hierarchically($pages, $page->id(), $with_children, $view_file);
			}
		}
	}
}
?>