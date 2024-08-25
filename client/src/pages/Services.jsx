import Header from "../components/Header"
import Footer from "../components/Footer"
import { useState, useEffect } from "react";
import axios from "axios";
import Popup from "../components/Popup";
import Chat from "../components/Chat";


const Services = () => {


    const [PopupVisibleId, setPopupVisibleId] = useState(null);
    const [users, setUsers] = useState([]);

    const token = sessionStorage.getItem('jwToken');


    const fetchuser = () => {
        const apiUrl = import.meta.env.VITE_API_URL;
        console.log("current env", apiUrl);

        axios.get(`${apiUrl}/customerbooking`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
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

<Chat/>

            <section className="bg-gradient-to-b from-white to-orange-100 rounded-md flex flex-col flex-grow justify-center items-center">

                <div className="py-8 px-4 mx-4  w-full">

                    <h2 className="mb-4 text-xl text-center font-bold text-gray-900 font-quick">Bookings</h2>

                    <ul className="grid grid-cols-1 sm:grid-cols-2  gap-4 px-px w-full">

                        {users.map(user => (
                            <li key={user.id} className=" bg-gradient-to-r from-orange-300 to-orange-200  flex flex-col  p-4 rounded-md ">

                                <p className="flex mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span className="font-bold">Customer Name</span>
                                    <span className="font-bold">{user.name}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-normal font-quick border-b border-stone-500">
                                    <span className="font-bold">Pickup Location</span>
                                    <span className="font-bold">{user.pickup}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-normal font-quick border-b border-stone-500">
                                    <span className="font-bold" >Dropoff Location</span>
                                    <span className="font-bold">{user.dropoff}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span className="font-bold">Car</span>
                                    <span className="font-bold">{user.car}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span className="font-bold">Passengers</span>
                                    <span className="font-bold">{user.passengers}</span>
                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap break-words font-quick border-b border-stone-500">
                                    <span className="font-bold">Description</span>

                                    <button onClick={() => openpopup(user.id)} > check </button>

                                    {
                                        PopupVisibleId === user.id &&
                                        <Popup isOpen={true} message={user.description} onClose={closePopup} />
                                    }


                                </p>

                                <p className="flex  mb-2 justify-between w-full whitespace-nowrap font-quick border-b border-stone-500">
                                    <span className="font-bold">Status</span>
                                    <span className="font-bold">{user.status}</span>
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