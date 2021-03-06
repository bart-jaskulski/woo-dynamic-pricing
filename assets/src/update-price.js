/**
 * Class for updating price on button click.
 */
class UpdatePrice {

	actionName = 'update_cart';
	_nonce = wooPrice.dynamicPriceNonce;
	_endpoint = woocommerce_params.ajax_url;
	_form = document.getElementById( 'add_to_cart_form' );
	_ticketType = document.getElementById( 'typ-biletu' );
	_quantity = document.getElementsByClassName( 'quantity' )[ 0 ].children.quantity;

	/**
	 * Class constructor. Stores button in object and fires event listener.
	 * @param  {[HTMLElement]} button Button to which you attach class with event.
	 */
	constructor(button) {
		this.button = button;

		this.addEventListener();
	}

	/**
	 * Update prices with ajax call.
	 */
	update() {
		if ( ! this.isButtonEnabled() ) {
			return;
		}
		// Disable button visually and start processing animation.
		this.toggleButtonCss()
		this.toggleFormCss()

		const [{ variation_id, display_price }] = this.getCurrentVariantData( this._ticketType.value );
		const ajaxParams = {
			method: "POST",
			credentials: 'same-origin',
			body: new URLSearchParams( {
				action: this.actionName,
				type: this._ticketType.value,
				quantity: this._quantity.value,
				product_id: variation_id,
				price: display_price,
				dynamicPriceNonce: this._nonce,
			} ),
		}

		let fetchPrices = this.fetchUpdatedPrices( ajaxParams )

		fetchPrices.then(
			response => {
				if ( response.success ) {
					const discountAmount = document.getElementById(	'discount_amount' );
					const singleTicketAmount = document.getElementById(	'single_ticket_amount' );
					const overallPrice = document.querySelector( '.woocommerce-Price-amount' );

					discountAmount.innerText = `${response.data.discount}%`;
					singleTicketAmount.innerHTML = response.data.single_price;
					overallPrice.outerHTML = response.data.total_price;

					// Remove class when finished.
					this.toggleFormCss()
				}
			}
		)
	}

	/**
	 * Call server to get new price data.
	 * @param  {[object]} data AJAX parameters for fetch function.
	 * @return {Promise} [description]
	 */
	async fetchUpdatedPrices( data ) {
		let response = await fetch( this._endpoint, data )
		try {
			return await response.json();
		} catch (e) {
			console.error( e )
		}
	}

	/**
	 * Get attributes associated with given product variant.
	 * @method getCurrentVariantData
	 * @param  {[string]} variantName Variant to retrieve data.
	 * @return {[array]} Array of variants.
	 */
	getCurrentVariantData( variantName ) {
		const formData = JSON.parse( this._form.dataset.product_variations );
		return formData.filter( item => {
			if ( item.attributes["attribute_typ-biletu"] === variantName ) {
				return true;
			}
			return false;
		})
	}

	/**
	 * Is button currently marked as enabled?
	 * @return {Boolean} Return true if button enabled.
	 */
	isButtonEnabled() {
		return ! this.button.classList.contains( 'is-disabled' );
	}

	/**
	 * Switch form container CSS class.
	 */
	toggleFormCss() {
		if ( ! this._form.classList.contains( 'processing' ) ) {
			this._form.classList.add( 'processing' )
		} else {
			this._form.classList.remove( 'processing' )
		}
	}

	/**
	 * Swith button CSS class.
	 */
	toggleButtonCss() {
		if ( ! this.button.classList.contains( 'is-disabled' ) ) {
			this.button.classList.add( 'is-disabled' )
		} else {
			this.button.classList.remove( 'is-disabled' )
		}
	}

	/**
	 * Attach update to button click events.
	 */
	addEventListener() {
		this.button.addEventListener( 'click', this.update.bind( this ) )
	}
}

new UpdatePrice(document.getElementById( 'update_cart' ))
