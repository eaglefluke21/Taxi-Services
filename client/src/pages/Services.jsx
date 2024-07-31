import Header from "../components/Header"
import Footer from "../components/Footer"
import { useState, useEffect } from "react";
import axios from "axios";
import Popup from "../components/Popup";



const Services = () => {


    const [PopupVisibleId, setPopupVisibleId] = useState(null);
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    const [users, setUsers] = useState([]);


    const fetchuser = () => {
        axios.get('http://localhost:8000/api/customerbooking.php')
            .then(response => {
                setUsers(response.data);
            })
            .catch(error => {
                console.error('There was an error fetching the users!', error);
            });
    };

    useEffect(() => {
        fetchuser();
    }, []);


    const openpopup = (userId) => {

        setPopupVisibleId(userId);
       
    };

    const closePopup = () => {
        setPopupVisibleId(null);
        
    };



    return (

        <div className="flex flex-col min-h-screen">

            <Header />



            <section className="bg-gradient-to-b from-white to-sky-100 rounded-md flex flex-col flex-grow justify-center items-center">

                <div className="py-8 px-4 mx-4  w-full">

                    <h2 className="mb-4 text-xl text-center font-bold text-gray-900 font-quick">Bookings</h2>

                    <ul className="grid grid-cols-1 sm:grid-cols-2  gap-4 px-px w-full">

                        {users.map(user => (
                            <li key={user.id} className=" bg-gradient-to-r from-gray-300 to-white  flex flex-col  p-4 rounded-md ">

                                <p className="flex mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span>Customer Name</span>
                                    <span className="font-bold">{user.name}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-normal font-quick border-b border-stone-500">
                                    <span>Pickup Location</span>
                                    <span className="font-bold">{user.pickup}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-normal font-quick border-b border-stone-500">
                                    <span className="font-medium" >Dropoff Location</span>
                                    <span className="font-bold">{user.dropoff}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span>Car</span>
                                    <span className="font-bold">{user.car}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span className="font-medium">Passengers</span>
                                    <span className="font-bold">{user.passengers}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap break-words font-quick border-b border-stone-500">
                                    <span>Description</span>

                                    <button onClick={() => openpopup(user.id)} > check </button>

                                    {
                                        PopupVisibleId === user.id &&
                                        <Popup isOpen={true} message={user.description} onClose={closePopup} />
                                    }


                                </p>


                            </li>
                        ))}

                    </ul>

                </div>

            </section>




            <Footer />

        </div>


    )

}

export default Services;