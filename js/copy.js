function copyInviteCode() {
    const input = document.getElementById("inviteCode");
    navigator.clipboard.writeText(input.value)
        .then(() => {
            alert("Invite code copied: " + input.value);
        })
        .catch(err => {
            console.error("Failed to copy text: ", err);
        });
}