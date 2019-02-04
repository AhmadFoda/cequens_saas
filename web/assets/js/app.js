// assets/js/app.js

require('../css/style.css');

console.log('Hello Webpack Encore');

// ---------------------------------------------
// Chatkit Code
// ---------------------------------------------


const tokenProvider = new Chatkit.TokenProvider({
	url: `https://us1.pusherplatform.io/services/chatkit_token_provider/v1/bb957f41-bf24-4f23-a015-1f51ceafb1b2/token`
})

const chatManager = new Chatkit.ChatManager({
	instanceLocator: "v1:us1:bb957f41-bf24-4f23-a015-1f51ceafb1b2",
	userId: "k.mohamed@cequens.com",
	tokenProvider
})

chatManager.connect().then(user => {
	setUser(user)
	user
		.subscribeToRoom({
			roomId: user.rooms[0].id,
			hooks: { onNewMessage: addMessage },
			messageLimit: 10
		})
		.then(setRoom)
})


// ---------------------------------------------
// Application Code
// ---------------------------------------------


const { app, h } = hyperapp
/* @jsx h */

const state = {
	user: {
		avatarURL:
			"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
	},
	room: {},
	messages: []
}

const actions = {
	setUser: user => ({ user }),
	setRoom: room => ({ room }),
	addMessage: payload => ({ messages }) => ({
		messages: [payload, ...messages]
	})
}

const { addMessage, setUser, setRoom } = app(
	state,
	actions,
	view,
	document.body
)




