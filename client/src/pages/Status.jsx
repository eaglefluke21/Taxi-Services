import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Header from '../components/Header.jsx';
import Footer from '../components/Footer.jsx';

const Status = () => {
    const [driverInfo, setDriverInfo] = useState(null);

    const [isAvailable, setIsAvailable] = useState(true);

    useEffect(() => {
        const fetchDriverInfo = async () => {
            try {
                const token = sessionStorage.getItem('jwToken');
                console.log(token);
                const response = await axios.get('http://localhost:8000/api/driverstatus.php', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                setDriverInfo(response.data);
                console.log("response data ", response.data);
                setIsAvailable(response.data.is_available);
            } catch (error) {
                console.error('Error fetching driver info:', error);
            }
        };

        fetchDriverInfo();
    }, []);

    const handleAvailabilityChange = async () => {

        const newAvailability = !isAvailable;

        try {
            const token = sessionStorage.getItem('jwToken');
            await axios.put('http://localhost:8000/api/driverstatus.php', {
                is_available: newAvailability
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });
            setIsAvailable(newAvailability);
        } catch (error) {
            console.error('Error updating availability:', error);
        }
    };



    return (
        <div className="flex flex-col min-h-screen">
            <Header />

            <div className="flex flex-col items-center flex-grow justify-center rounded-lg bg-white lg:pb-0">
                <h1 className="text-xl text-center font-anta text-black pt-4">Driver Status</h1>
                {driverInfo ? (

                    
                    <div>
                        <p>Name: {driverInfo.name}</p>
                        <p>Status: {isAvailable ? 'Available' : 'Not Available'}</p>
                        <button onClick={handleAvailabilityChange} className="bg-black text-white rounded-md px-4 py-2 mt-4">
                            {isAvailable ? 'Mark as Not Available' : 'Mark as Available'}
                        </button>
                    </div>
                ) : (
                    <p>Loading...</p>
                )}
            </div>

            <Footer />
        </div>
    );
};

export default Status;
