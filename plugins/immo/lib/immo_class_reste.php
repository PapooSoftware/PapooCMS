				if (is_object($single_data))
				{
					foreach ($single_data as $key2=>$value2)
					{
						//$typ[$key2]=$key2;array_keys
						if (is_object($value2))
						{
							foreach ($value2 as $key3=>$value3)
							{
								#debug::print_d($value3);
								if (is_object($value3))
								{
									foreach ($value3 as $key4=>$value4)
									{
										if (is_object($value4))
										{
											
											$data3[]=array_keys(get_object_vars($value4));
										}
										else
										{
											$neu[$key4]=$key4;
										}
										
									}

									//debug::print_d($data2);
								}
								else
								{
									$neu[$key3]=$key3;
								}
							}

						}
						else
						{
							$neu[$key2]=$key2;
						}
					}
				}
				
				
				if (is_object($value2))
		{
			foreach ($value2 as $key1=>$value1)
			{
				//Wenn es ein Objekt ist
				if (is_object($value1))
				{
					foreach ($value1 as $key3=>$value3)
					{
						//Falls es ein Spezialfall ist
						if (in_array($key3,$specials))
						{
							$value3=$this->get_special($value3,$key3);
							$vorlage=str_ireplace("#".$key3."#",$value3,$vorlage);
						}
						
						//Falls Adresse nicht angezeigt werden soll
						if ($key3=="street" && !$show_address===true)
						{
							continue;
						}
						if ($key3=="houseNumber" && !$show_address===true)
						{
							continue;
						}
						if (is_object($value3))
						{
							foreach ($value3 as $key4=>$value4)
							{
								if (!is_object($value4))
								{
									//Daten ersetzen
									$vorlage=str_ireplace("#".$key4."#",$value4,$vorlage);
								}
								
							}
						}
						else
						{
							//Daten ersetzen
							$vorlage=str_ireplace("#".$key3."#",$value3,$vorlage);
						}
						//debug::print_d($value3);

						#
						
					}
				}
				//Direkt Daten
				else
				{
					$vorlage=str_ireplace("#".$key1."#",$value1,$vorlage);
					
				}
				
			}
		}