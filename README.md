# DX Deploy Notification
Display message to the frontend set by the administrator through WP_CLI. Every time you add new notification the old one will be overwritten. The main function of this plugin is to give clear message that there has been change or deployment to the server. The person who pushes to the server runs the commands below, removing the questions if there has been new changes to the server.

### Usage
`wp msg content "Your message"` - Add new notification window.
`wp msg clear` - Remove the recently added notification.