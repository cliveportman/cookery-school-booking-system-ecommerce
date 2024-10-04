<?php
namespace Craft;

class RegistrationService extends BaseApplicationComponent
{

	// SEND NOTIFICATIONS TO PEOPLE LISTED
	public function sendNotification($options = array()) 
	{

		// SEND THE EMAIL
        $email = new EmailModel();
        $email->toEmail = $options['email'];
        $email->subject = $options['subject'];
        $email->body    = $options['body'];
        craft()->email->sendEmail($email);

	}

	// SEND QUICK NOTIFICATION
	public function sendAutoNotification($subject) 
	{

		// SEND THE EMAIL
        $email = new EmailModel();
        $email->toEmail = 'clive@webandpen.uk';
        $email->subject = $subject;
        $email->body    = 'This is an automatic notification with the subject "' . $subject . '"';
        craft()->email->sendEmail($email);

	}

	// ADD THE EMPTY REGISTERS TO A NEW ORDER
	public function createOrderRegisters($order) 
	{

		$lineItems = $order->lineItems;
		$address = $order->shippingAddress;
		$name = "$address->firstName $address->lastName";
		$message = count($lineItems) . ' line items: ';
		$registersData = array();
		$lineItemRow = 0;

		foreach ($lineItems as $item) {
			$lineItemRow++;
			$message .= '#';
			$message .= $item->id;
			$purchasable = $item->purchasable;
			$product = $purchasable->product;
			$message .= '(' . $product->id . ')';
			
			// PREP THE REGISTER'S PARTICIPANTS TABLE
			$participantTableRows = [];
			for ($participant = 0; $participant < $item->qty; $participant++) {
				// IF CUSTOMER
				if ($participant == 0) {
					$participantTableRows[$participant] = array('col1' => $name, 'col2' => 0);
				} else {
			    	$participantTableRows[$participant] = array('col1' => '', 'col2' => 0);
				}
			}

			$registersData['new' . $lineItemRow] = array(
			  'type' => 'register',
			  'enabled' => true,
			  'fields' => array(
			  	'notes' => '',
			    'product' => [$product->id],
			    'participants' => $participantTableRows
			  )
			); 
		}

		// AM USING A TEST FIELD TO CHECK SHIZZLE AT THE MOMENT BUT REMOVE BEFORE LAUNCH
		$order->setContentFromPost(array(
		    'testField' => $message,
		    'registers' => $registersData
		));

        craft()->commerce_orders->saveOrder($order);		

	}

	// UPDATES AN ORDER'S REGISTERS WITH THE PARTICIPANTS
	public function updateParticipants($vars) 
	{

		$order = craft()->commerce_orders->getOrderById($vars['orderId']);
		$products = $vars['products']; // actually, the products refer to registers


		if ($order) {

			$registersData = array();
			foreach ($products as &$product) {

				//throw new Exception(Craft::t(print_r($product)));

				// get the updated participants information from the post
				$participants = $product['participants'];
				$updatedTable = [];
				foreach ($product['participants'] as $row) {
					$updatedTable[] = array('col1' => $row['name'], 'col2' => $row['present']);
				}

				// get the matrix block, set the content and save it 			
				$block = craft()->matrix->getBlockById($product['register']);
				$block->getContent()->setAttributes(array(
				    'participants' => $updatedTable,
				    'notes' => $product['notes']
				));
				$response = craft()->matrix->saveBlock($block);


				// RATHER THAN FETCH THE EXISTING VALUES AND CHECKING THEM USING
				// $participantTable = $block->getFieldValue('participants');
				// foreach ($participantTable as $key => $row) {
					// $updatedRows[] = array('col1' => 'p001', 'col2' => 0, 'col3' => 1);
				// }
				//throw new Exception(Craft::t(print_r($participantTable)));
				//$updatedRows = [];
				//$updatedRows[] = array('col1' => 'p001', 'col2' => 0, 'col3' => 1);
				//$block->getContent()->setAttributes(array(
				    //'participants' => $updatedTable,
				    //'notes' => json_encode($updatedTable)
				//));
			}
			/*
			$registersData = array();
			foreach ($products as &$register) {
				$participantTableRows[0] = array('col1' => 'p001', 'col2' => 0, 'col3' => 1);
				$registersData[$register] = array(
				  'type' => 'register',
				  'enabled' => true,
				  'fields' => array(
				    'participants' => $participantTableRows
				  )
				);
			}
			$order->getContent()->setAttribute('registers', $registersData);
			*/
			$order->getContent()->setAttribute('testField', '001');
	        craft()->commerce_orders->saveOrder($order);	
			return TRUE;
		} else {
			throw new Exception(Craft::t("No order"));			
		}
	}



	// RECORD ATTENDENCE USING THE REGISTERS WITHIN THE ORDERS' CUSTOM FIELDS
	// THE REGISTERS ARE MATRIX BLOCKS, ONE FOR EACH PRODUCT WITHIN AN ORDER,
	// EACH REGISTER CONTAINS A TABLE
	// EACH TABLE HAS A ROW FOR EACH PARTICIPANT
	// EACH ROW HAS A 'NAME' COLUMN (TEXT) AND A 'PRESENT' COLUMN (CHECKBOX)
	// CALLED ON:
	// templates/bookings/day
	public function updateAttendance($vars) 
	{

		$product = $vars['product'];
		
		/*
		THE PRODUCT DATA CURRENTLY LOOKS LIKE THIS
		$product =
			productId // CURRENTLY UNUSED
			registers = array [
				registerId
				participants [
					[i] = 0 if not present
					[i] = 1 if present
				]
			]
			orderId // CURRENTLY UNUSED

		USE THIS FOR DEBUG:
		throw new Exception(Craft::t(print_r($product)));
		*/

		// LOOP THROUGH EACH REGISTER
		foreach ($product['registers'] as &$register) {	

			// FETCH THE MATRIX BLOCK (AKA THE REGISTER	)
			$matrixBlock = craft()->matrix->getBlockById($register['registerId']);

			// CREATE THE UPDATED TABLE
			$updatedTable = [];
			foreach ($register['participants'] as $row) {
				$updatedTable[] = array('col1' => $row['name'], 'col2' => $row['present']);
			}

			// UPDATE THE MATRIX BLOCK CONTENT
			$matrixBlock->getContent()->setAttributes(array(
			    'participants' => $updatedTable
			));

			// SAVE THE MATRIX BLOCK, NOT THE ORDER!
			$saveMatrixBlock = craft()->matrix->saveBlock($matrixBlock);

		}

		return TRUE;
	}

	// DISABLES A REGISTER
	// THE REGISTERS ARE MATRIX BLOCKS, ONE FOR EACH PRODUCT WITHIN AN ORDER
	// AND THIS FUNCTION LOOKS FOR A REGISTER CONTAINING THE SAME PRODUCT AS A LINE ITEM
	// AND SETS IT'S ENABLED ATTRIBUTE TO FALSE
	// THERE IS NO VARIABLE FOR THIS - IT'S CALLED WITHIN A PLUGIN USING:
	// craft()->registration->disableRegister($order, $lineItem);
	public function disableRegister(Commerce_OrderModel $order, Commerce_LineItemModel $lineItem) 
	{

		// GET THE PRODUCT SO WE CAN FIND WHICH REGISTER TO CANCEL
		$purchasable = $lineItem->purchasable;
		$product = $purchasable->product;

		// GET THE REGISTERS
		$registers = $order->registers;

		// LOOP THROUGH THE REGISTERS
		foreach ($registers as $register) {

			// LOOP THROUGH THE PRODUCTS WITHIN THE REGISTER (SHOULD ONLY BE ONE)
			foreach ($register->product as $registerProduct) {

				// IF THE PRODUCT MATCHES THE ONE WE WANT TO DISABLE
				if ($registerProduct->id == $product->id) {

					// DISABLE THE REGISTER
					$register->enabled = FALSE;
					$saveMatrixBlock = craft()->matrix->saveBlock($register);

			        $message = 'Disabled register with ID #' . $register->id . ' within order # ' . $order->id;
		            StockReplenisherPlugin::log($message, LogLevel::Info);
				}
			}
		}

		return TRUE;

	}

	// REMOVES ROWS FROM THE PARTICIPANTS TABLE
	// THE REGISTERS ARE MATRIX BLOCKS, ONE FOR EACH PRODUCT WITHIN AN ORDER
	// AND THIS FUNCTION LOOKS FOR A REGISTER CONTAINING THE SAME PRODUCT AS A LINE ITEM
	// THEN TAKES THE NUMBER OF ROWS TO BE REMOVED AND REMOVES THEM
	// FROM THE PARTICIPANTS TABLE
	// THERE IS NO VARIABLE FOR THIS - IT'S CALLED WITHIN A PLUGIN USING:
	// craft()->registration->disableRegister($order, $lineItem, $numRowsToRemove);
	public function removeRowsFromRegister(Commerce_OrderModel $order, Commerce_LineItemModel $lineItem, $numRowsToRemove = 0) 
	{

		// GET THE PRODUCT SO WE CAN FIND WHICH REGISTER TO UPDATE
		$purchasable = $lineItem->purchasable;
		$product = $purchasable->product;

		// GET THE REGISTERS
		$registers = $order->registers;

		// LOOP THROUGH THE REGISTERS
		foreach ($registers as $register) {

			// LOOP THROUGH THE PRODUCTS WITHIN THE REGISTER (SHOULD ONLY BE ONE)
			foreach ($register->product as $registerProduct) {

				// IF THE PRODUCT MATCHES THE ONE WE WANT TO UPDATE
				if ($registerProduct->id == $product->id) {

					// FETCH THE MATRIX BLOCK (AKA THE REGISTER	)
					$matrixBlock = craft()->matrix->getBlockById($register->id);

					// CREATE THE UPDATED TABLE
					$updatedTable = [];

					$rowIndex = 1;
					$newParticipantsTotal = count($register->participants) - $numRowsToRemove;

					foreach ($register->participants as $row) {
						if ($rowIndex <= $newParticipantsTotal) {
							$updatedTable[] = array('col1' => $row['name'], 'col2' => $row['present']);
						}
						$rowIndex++;
					}

					// UPDATE THE MATRIX BLOCK CONTENT
					$matrixBlock->getContent()->setAttributes(array(
					    'participants' => $updatedTable
					));

					// SAVE THE MATRIX BLOCK, NOT THE ORDER!
					$saveMatrixBlock = craft()->matrix->saveBlock($matrixBlock);

				}
			}
		}

		return TRUE;

	}

}