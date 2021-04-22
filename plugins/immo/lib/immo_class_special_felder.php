<?php

/**
 * Class immo_special
 */
class immo_special
{
	/**
	 * immo_special constructor.
	 */
	function __construct()
	{
		return true;
	}

	/**
	 * @param mixed $data
	 * @param mixed $key
	 * @return string
	 */
	public function get_special($data,$key)
	{
		//Kaufpreis
		if (stristr($key,"value")) {
			$data=number_format($data,2,",",".");
		}

		//Bau
		if (stristr($key,"constructionPhase")) {
			if ($data=="COMPLETED") {
				$data="Fertig gestellt";
			}
		}

		if (stristr($key,"petsAllowed")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"negotiable"=>"Nach Vereinbarung"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"apartmentType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"roof_storey"=>"Dachgeschoss Wohnung"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"firingType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"gas"=>"Gas Heizung",
				"oil"=>"Öl Heizung"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"gastronomyType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"hotel_residence"=>"Hotel",
				"oil"=>"Öl Heizung"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		//apartmentType Dachgeschoss

		//Typ
		if (stristr($key,"buildingType") || stristr($key,"investmentType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"single_family_house"=>"Einfamilienhaus",
				"mid_terrace_house"=>"Einfamilienhaus",
				"end_terrace_house"=>"Einfamilienhaus",
				"multi_family_house"=>"Mehrfamilienhaus",
				"bungalow"=>"Bungalow",
				"farm_house"=>"Bauernhof",
				"semidetached_house"=>"Doppelhaushälfte",
				"villa"=>"Villa",
				"castle_manor_house"=>"Herrenhaus",
				"special_realestate"=>"besondere Immobilien",
				"other_realestate"=>"andere Immobilien",
				"terrace_house"=>"Reihenhaus"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		//Zustand
		if (stristr($key,"condition")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"well_kept"=>"Gut",
				"fully_renovated"=>"Voll renoviert"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"interiorQuality")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"normal"=>"Normal",
				"simple"=>"Einfach",
				"sophisticated"=>"Gehoben"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"heatingType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"central_heating"=>"Zentral Heizung",
				"self_contained_central_heating"=>"Etagenheizung"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"energySources")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"gas"=>"Gas",
				"oil"=>"Öl"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"siteDevelopmentType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"developed"=>"Erschlossen"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"recommendedUseTypes.siteRecommendedUseType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"single_family_house"=>"Einfamilienhaus"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"storeType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"sales_hall"=>"Verkaufsraum"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"industryType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"hall"=>"Halle"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"commercializationType")) {
			$data=trim(strtolower($data));
			$bt_ra=array(
				"rent"=>"Miete",
				"buy"=>"Kauf"
			);
			$x=$bt_ra[$data];
			$data=$x;
		}

		if (stristr($key,"buildingEnergyRatingType")) {
			if ($data=="NO_INFORMATION") {
				$data="Keine Information";
			}

			if ($data=="ENERGY_CONSUMPTION") {
				$data="Verbrauchsausweis";
			}

			if ($data=="ENERGY_REQUIRED") {
				$data="Bedarfsausweis";
			}
		}

		if (stristr($key,"parkingSpaceType")) {
			if ($data=="GARAGE") {
				$data="Garage";
			}

			if ($data=="UNDERGROUND_GARAGE") {
				$data="Tiefgarage";
			}

			if ($data=="OUTSIDE") {
				$data="Außen";
			}
		}

		if (stristr($key,"marketingType")) {
			if ($data=="PURCHASE") {
				$data="Kauf";
			}
		}

		if (stristr($key,"priceIntervalType")) {
			if ($data=="ONE_TIME_CHARGE") {
				$data="Einmal Preis";
			}
		}

		//Ja / Nein Einstellungen
		if (stristr($key,"cellar")
			|| stristr($key,"hasCourtage")
			|| stristr($key,"guestToilet")
			|| stristr($key,"builtInKitchen")
			|| stristr($key,"certificateOfEligibilityNeeded")
			|| stristr($key,"terrace")
		) {
			if ($data=="YES") {
				$data="Ja";
			}
			else {
				$data="Nein";
			}
		}

		if (stristr($key,"garden")
			|| stristr($key,"certificateOfEligibilityNeeded")
			|| stristr($key,"lift")
			|| stristr($key,"balcony")
			|| stristr($key,"shortTermConstructible")
		) {
			if ($data=="true") {
				$data="Ja";
			}
			else {
				$data="Nein";
			}
		}
		return $data;
	}
}

$immo_special = new immo_special();
