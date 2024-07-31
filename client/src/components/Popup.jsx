import React from "react";


import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
  } from "@/components/ui/alert-dialog"



const Popup = ({isOpen ,message,onClose}) => {

    return(
        
    <AlertDialog open={isOpen}>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle className="font-quick">{message}</AlertDialogTitle>
      </AlertDialogHeader>
      <AlertDialogFooter>
        
        <AlertDialogAction onClick={onClose} >Close</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
  
    );









};

export default Popup;