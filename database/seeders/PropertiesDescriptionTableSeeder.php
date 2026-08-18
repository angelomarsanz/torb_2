<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesDescriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_description')->truncate();
    	
        DB::table('property_description')->insert([
            
            [
            'property_id' => 1,
            'summary' => 'A stay at Hampton Inn Times Square North places you in the heart of New York, walking distance from Studio 54 and Ed Sullivan Theater. This hotel is close to Broadway and Rockefeller Center.',
            'place_is_great_for' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'about_place' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'guest_can_access' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'interaction_guests' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'other' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'about_neighborhood' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'get_around' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.'
            ], 
                        
            [
            'property_id' => 2,
            'summary' => 'The View Hotels comprise three hotels within Australia located in three of the most beautiful and exciting cities – Sydney, Melbourne and Brisbane.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],     
                        
            [
            'property_id' => 3,
            'summary' => 'Situated in the famous Marais district surrounded by boutiques, monuments and museums, the Hotel Paris Rivoli offers three-star accommodations in the most desirable part of Paris.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
                                    
            [
            'property_id' => 4,
            'summary' => 'K+K Picasso offers 4-star elegance in Barcelona’s El Born district, directly opposite Parc de la Ciutadella and Barcelona Zoo on Passeig de Picasso. The hotel has avant-garde architecture, a rooftop pool with city views and is less than 15 minutes’ walk from La Rambla, Barceloneta Beach and the Gothic Quarter.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL,
            ],
                    
            [
            'property_id' => 5,
            'summary' => 'CONTACT APEX HOTELS',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
                                        
            [
            'property_id' => 6,
            'summary' => 'Set in the SoMA neighborhood, this straightforward hotel with an annex is 1 mile from Union Square\'s shopping, 1.5 miles from Chinatown and 2.5 miles from Fisherman\'s Wharf\'s seafood restaurants.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
                                    
            [
            'property_id' => 7,
            'summary' => 'Private luxury bungalow with astonishing view , cozy in winter cold covered with snow and chill in the summer with private pool .Excellent location for celebration of small and big group enjoying chimney fire as well as spacious terrace For ski lovers , it is the perfect location 10 min away from faraya where road are always open even with extreme winter condition',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
                        
            [
            'property_id' => 8,
            'summary' => 'Experience the best of Brooklyn’s creative scene just steps from this renovated, furnished apartment, fully equipped with everything you need to get settled in — all you have to bring is your suitcase! Walk outside and find yourself in the heart of Bushwick, renowned for its street art and bustling community. This apartment includes access to the backyard, perfect for getting time outdoors, and is just a short walk from the neighborhood’s largest park.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL,
            
            ],          
                        
            [
            'property_id' => 9,
            'summary' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et fermentum dui. Ut orci quam, ornare sed lorem sed, hendrerit auctor dolor. Nulla viverra, nibh quis ultrices malesuada, ligula ipsum vulputate diam, aliquam egestas nibh ante vel dui. Sed in tellus interdum eros vulputate placerat sed non enim. Pellentesque eget.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],     
                        
            [
            'property_id' => 10,
            'summary' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et fermentum dui. Ut orci quam, ornare sed lorem sed, hendrerit auctor dolor. Nulla viverra, nibh quis ultrices malesuada, ligula ipsum vulputate diam, aliquam egestas nibh ante vel dui. Sed in tellus interdum eros vulputate placerat sed non enim. Pellentesque eget.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],      
                        
            [
            'property_id' => 11,
            'summary' => 'Private room in a beautiful house in Queens. located 5min walk from the metro J, Z Subway (24/7), around 40-45min to Downtown Manhattan (9/11 Memorial, Wall Street). 50min to times square and central park. Easy to get anywhere with public transport.15 min from JFK with direct J train. While staying relatively close to all touristic sites seeing\'s you get to stay in real New York, feel, smell and taste NYC by passing through off the beaten tourist tracks, eating local food & interact with locals.',
            'place_is_great_for' => NULL,
            'about_place' => 'The bedroom is pretty quiet. The bedroom has a Queen-size bed and a very comfortable arm-chair. The room is pretty big, cozy and very comfortable. The room located on the 1st floor, next to the living room, has 3 windows and a closet (luggage and clothes). The fireplace made the room very warm and romantic (please, do not try to ignite the fire). Bed is Queen size with memory mattress and it\'s really great for sleeping. Visitors can use big open spaces on the first floor. The bathroom has a shower with strong water pressure.',
            'guest_can_access' => 'Wi-Fi, Heating, Air Conditioner, and utilities are included in price.\r\nNO pets, No smoking.\r\nThere is a free parking area around the house available on the street in the neighborhood.\r\nIn a basement are washing machine and drier - for Airbnb guests it\'s free.',
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],     
                        
            [
            'property_id' => 12,
            'summary' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et fermentum dui. Ut orci quam, ornare sed lorem sed, hendrerit auctor dolor. Nulla viverra, nibh quis ultrices malesuada, ligula ipsum vulputate diam, aliquam egestas nibh ante vel dui. Sed in tellus interdum eros vulputate placerat sed non enim. Pellentesque eget.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],     
                        
            [
            'property_id' => 13,
            'summary' => 'Furnished Junior Suite with private bath and fully equipped kitchen in midtown. 3 blocks from Grand Central Station. All utilities, cable, wifi, local calls, weekly linen service, toiletries, in-room coffee and tea supplies and access to gym and business center included in price. Housekeeping service is available for an extra fee. Doorman and 24 Hour Reception and Concierge Desk. Onsite laundry room. Supermarket across street. Approx 9.5% NYC taxes to be paid directly to the property.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],      
                                
            [
            'property_id' => 14,
            'summary' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et fermentum dui. Ut orci quam, ornare sed lorem sed, hendrerit auctor dolor. Nulla viverra, nibh quis ultrices malesuada, ligula ipsum vulputate diam, aliquam egestas nibh ante vel dui. Sed in tellus interdum eros vulputate placerat sed non enim. Pellentesque eget.',
            'place_is_great_for' => NULL,
            'about_place' => NULL,
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],      
                        
            [
            'property_id' => 15,
            'summary' => 'Only 7 minutes to NYC, Port Authority! One block to all public transportation. Amazing View of the New York City Skyline! Private bedroom, with shared living room, kitchen and bathroom with only one person. Very quiet and spacious!',
            'place_is_great_for' => NULL,
            'about_place' => 'Private professional single or two people. There is a private terrace in the back, and the living room attached to private bedroom room that shows the view is great for reading and private time, yoga, meditating!',
            'guest_can_access' => 'Wi-Fi, Heating, Air Conditioner, and utilities are included in price.\r\nNO pets, No smoking.\r\nThere is a free parking area around the house available on the street in the neighborhood.\r\nIn a basement are washing machine and drier - for Airbnb guests it\'s free.',
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
                                
            [
            'property_id' => 16,
            'summary' => 'Appointed with sleek furniture, our classic rooms offer the traditional comforts of home in one of the most time-honored hotels in New York City.\r\n\r\nYour room charge has been taken prior to your arrival as a deposit. The remaining balance of the taxes (NY Sales Tax: 8.875%, NY City Tax: 5.875%, Occupancy Tax: $2.00 & Javits Fee: $1.50) and facility fee ($28.68) per night will be charged upon arrival.\r\n\r\nGuests must be at least 21 yrs of age to check in. Gov. -issued photo ID is required.',
            'place_is_great_for' => NULL,
            'about_place' => 'This category has three options available, 1 King bed, 2 Double Beds or ADA accessible with a spacious bathroom. Both business and leisure travelers requiring accommodation in New York City will appreciate one of our most popular room types.',
            'guest_can_access' => NULL,
            'interaction_guests' => NULL,
            'other' => NULL,
            'about_neighborhood' => NULL,
            'get_around' => NULL
            ],
        ]);
    }
}
