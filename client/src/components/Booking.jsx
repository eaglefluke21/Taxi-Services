import React from "react";
import DetailsBooking from "./DetailsBooking";
import taxiImage from "../assets/maintaxi.svg"

const Booking = () => {


    return (
        <>
            <div className="flex flex-row justify-center lg:items-center  rounded-md py-6 " >

               

                <div className="flex flex-col  lg:flex-row items-center lg:gap-32">

                    <div>
                        
                        <DetailsBooking/>
                        
                    </div>

                    <div className="lg:flex lg:flex-col lg:gap-20 lg:w-[32rem] lg:mb-20   "> 

                        <h1 className=" text-3xl text-center font-anta text-black invisible lg:visible">
                       
                        <img src={taxiImage} />

                        </h1>


                    </div>


                </div>



            </div>




        </>
    );

}

export default Booking;