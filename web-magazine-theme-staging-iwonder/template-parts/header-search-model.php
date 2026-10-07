<div class="search-model-container">
	
	<div class="ms-lg-3 search-panel web-search">

		<div class="search-panel-wrapper">
			<i class="fa-solid fa-magnifying-glass"></i>
		</div>

		<form 
			role="search" 
			method="get" 
			class="d-flex hw-search" 
			action="<?php echo esc_url( home_url( '/' ) ); ?>"
		>

			<div class="input-group flex-nowrap">

				<input 
					type="search"
					class="global-search"
					placeholder="<?php echo esc_attr__( 'What are you looking for', 'textdomain' ); ?>"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					aria-label="<?php echo esc_attr__( 'Search', 'textdomain' ); ?>"
				>

				<button 
					type="button" 
					class="search-clear"
					aria-label="<?php echo esc_attr__( 'Clear Search', 'textdomain' ); ?>"
				>
					<i class="fa-solid fa-xmark"></i>
				</button>

				<button 
					class="btn btn-brand search-button rounded-0"
					type="submit"
					aria-label="<?php echo esc_attr__( 'Search', 'textdomain' ); ?>"
				>
					<span><?php esc_html_e( 'Search', 'textdomain' ); ?></span>
				</button>

			</div>

		</form>

	</div>

</div>
