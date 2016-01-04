<?php

/**
 * wp sweep is a command to sweep your environment and prepare it for a staging / development environment.
 *
 */
class WP_Sweep extends WP_CLI_Command {

	protected $tables;
	protected $formats;
	protected $limits;
	protected $dry_run = false;


	/**
	 * Call with wp sweep, no need for additional commands, only parameters per README
	 * @param $args
	 * @param $assoc_args
	 */
	function __invoke( $args, $assoc_args ) {
		do_action( 'wp_sweep_before_parse_arguments', $args, $assoc_args );
		$this->parse_arguments( $args, $assoc_args );
		do_action( 'wp_sweep_after_parse_arguments', $args, $assoc_args );
		$this->run();

	}

	/**
	 * Arguments parser for all supplied arguments
	 * @param $args
	 * @param $assoc_args
	 */
	function parse_arguments( $args, $assoc_args ) {
		while ( count( $args ) ) {
			$arg = array_shift( $args );
			switch ( $arg ) {
				case '-t':
					$this->parse_argument( $args, 'tables' );
					break;
				case '-f':
					$this->parse_argument( $args, 'formats' );
					break;
				case '-l':
					$this->parse_argument( $args, 'limits' );
					break;
			}
		}

		$this->dry_run = ! empty( $assoc_args[ 'dry-run' ] );
	}

	/**
	 * Parse an arg for an individual property, if it exists.
	 *
	 * @param $args
	 * @param $property
	 *
	 * @return mixed
	 */
	function parse_argument( $args, $property ) {
		do_action( 'wp_sweep_before_parse_argument_' . $property, $args );
		if ( property_exists( $this, $property ) && count( $args ) && '-' !== substr( $args[0], 0, 1 ) ) {
			$arg_values = explode( ',', array_shift( $args ) );
			$this->{ "$property" } = apply_filters( 'wp_sweep_argument_' . $property, array_unique( array_merge_recursive( (array) $this->{ "$property" }, $arg_values ) ) );
		}
		return $this->{ "$property" };

	}

	function run() {
		global $wpdb;
		WP_CLI::success( $wpdb->prefix );
		var_dump( $this->tables );
		var_dump( $this->formats );
		var_dump( $this->limits );
		var_dump( $this->dry_run );
		var_dump( get_editable_roles() );
	}
}

WP_CLI::add_command( 'sweep', 'WP_Sweep' );
