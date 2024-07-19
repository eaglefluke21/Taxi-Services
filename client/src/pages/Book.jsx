import Header from "../components/Header"
import Footer from "../components/Footer"
import Booking from "../components/Booking"

const Book = () => {

    return (

        <div className="flex flex-col min-h-screen ">

        <Header />
    
        <div className=" flex flex-col items-center flex-grow justify-center  rounded-lg bg-white lg:pb-0">
    
            <h1 className=" text-xl text-center font-anta text-black pt-4 lg:hidden ">
    
                Book Your Cab Now!!

               
    
            </h1>
    
            <Booking />
    
    
        </div>
    
        <Footer />
    
    
    
    </div>

    )

}

export default Book;