import Header from "../components/Header"
import Footer from "../components/Footer"
import { useState , useEffect } from "react";
import axios from "axios";
import Popup from "../components/Popup";

const Services = () => {


    const [isPopupVisible, setPopupVisible] = useState(false);

    
    const [users, setUsers] = useState([]);

    const fetchuser = () => {
        axios.get('http://localhost:8000/api/user.php')
            .then(response => {
                setUsers(response.data);
            })
            .catch(error => {
                console.error('There was an error fetching the users!', error);
            });
    };

    useEffect(() =>{
        fetchuser();
    },[]);


    const openpopup = () =>{

        setPopupVisible(true);
    };

    const closePopup = () => {
        setPopupVisible(false);
    };



    return (

        <div className="flex flex-col min-h-screen">

        <Header />
    
        <section className="rounded-md bg-white flex flex-col justify-center items-center">
    
            <div className="py-8 px-4 mx-4 max-w-5xl w-full">
    
                <h2 className="mb-4 text-xl text-center font-bold text-gray-900 font-quick">Bookings</h2>
    
                <ul className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
    
                    {users.map(user => (
                        <li key={user.id} className=" flex flex-col p-4 shadow-inner shadow-gray-100 rounded-md ">

                <p className="flex justify-between w-full whitespace-nowrap font-quick">
                            <span>Customer Name</span>
                            <span className="font-bold">{user.name}</span>
                        </p>
                        <p className="flex justify-between w-full whitespace-nowrap font-quick">
                            <span>Pickup Location</span>
                            <span className="font-bold">{user.pickup}</span>
                        </p>
                        <p className="flex justify-between w-full whitespace-nowrap font-quick">
                            <span>Dropoff Location</span>
                            <span className="font-bold">{user.dropoff}</span>
                        </p>
                        <p className="flex justify-between w-full whitespace-nowrap font-quick">
                            <span>Car</span>
                            <span className="font-bold">{user.car}</span>
                        </p>
                        <p className="flex justify-between w-full whitespace-nowrap font-quick">
                            <span>Passengers</span>
                            <span className="font-bold">{user.passengers}</span>
                        </p>
                        <p className="flex justify-between w-full whitespace-nowrap break-words font-quick">
                            <span>Description</span>
                            <span  onClick={openpopup} className="font-bold border-2 text-white bg-black rounded-md px-2"> check </span>
                            
                        </p>

                        <div>
                            {
                               isPopupVisible &&
                                  <Popup message={user.description} onClose={closePopup}/>
                             }

                            </div>
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